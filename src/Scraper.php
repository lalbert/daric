<?php

namespace Daric;

use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;

class Scraper
{
    /**
     * uri to scrape.
     *
     * @var string
     */
    protected $uri;

    /**
     * The final document with extracted data.
     *
     * @var Document
     */
    protected $document;

    /**
     * Final data extracted to scrape.
     *
     * @var array
     */
    protected $data = [];

    /**
     * @var Symfony\Component\DomCrawler\Crawler
     */
    protected $content;

    /**
     * @var Goutte\Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $clientConfig;

    /**
     * @var array
     */
    protected $extractors = [];

    /**
     * @var array
     */
    protected $cleaners = [];

    /**
     * @var array
     */
    protected $formatters = [];

    /**
     * Define if client must redirect to the canonical uri (if present) when
     * it scape a page.
     *
     * @default false
     *
     * @var bool
     */
    protected $followCanonical = false;

    public function __construct($uri = null, array $extractors = [], array $cleaners = [], array $formatters = [])
    {
        if (!\is_null($uri)) {
            $this->setUri($uri);
        }

        if (!empty($extractors)) {
            $this->setExtractors($extractors);
        }

        if (!empty($cleaners)) {
            $this->setCleaners($cleaners);
        }

        if (!empty($formatters)) {
            $this->setFormatters($formatters);
        }

        $this->document = new Document();
    }

    /**
     * Set uri to scrape.
     *
     * @param string $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Retrive uri to scrape.
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Scrape uri, extract all configured data, cleanup it and return Document.
     *
     * @param string $uri
     *
     * @throws \InvalidArgumentException If uri is not set
     *
     * @return Document
     */
    public function scrape($uri = null)
    {
        if (!\is_null($uri)) {
            $this->setUri($uri);
        }

        if (!$this->getUri()) {
            throw new \InvalidArgumentException('You must set uri before run scrape.');
        }

        $this->content = $this->getClient()->request('GET', $this->getUri());

        if ($this->followCanonical) {
            if (count($this->content->filter('link[rel="canonical"]')) > 0) {
                $canonical = $this->absolutizeUri($this->content->filter('link[rel="canonical"]')
                    ->attr('href'));
                if ($this->getUri() != $canonical) {
                    return $this->scrape($canonical);
                }
            }
        }

        $this->extract();
        $this->clean();
        $this->format();

        $this->document->setData($this->data);

        return $this->document;
    }

    /**
     * Set HTTP Client.
     *
     * @param Goutte\Client $client
     */
    public function setClient(\Goutte\Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get Goutte Client.
     *
     * @return Goutte\Client
     */
    public function getClient()
    {
        if (!$this->client) {
            if (!$this->client) {
                $this->client = new Client();
                $this->client->setClient(
                    new GuzzleClient($this->getClientConfig())
                );
            }
        }

        return $this->client;
    }

    /**
     * Set GuzzleClient config.
     *
     * @see http://docs.guzzlephp.org/en/latest/request-options.html
     *
     * @param array $config
     *
     * @return \Daric\Scraper
     */
    public function setClientConfig(array $config)
    {
        $this->clientConfig = $config;

        return $this;
    }

    /**
     * Retrieve client configuration. If configuration is not set, return the
     * default config.
     *
     * @return array;
     */
    public function getClientConfig()
    {
        if (!$this->clientConfig) {
            $this->clientConfig = [
                'allow_redirects' => true,
                'cookies' => true,
            ];
        }

        return $this->clientConfig;
    }

    /**
     * Set if scraper must be follow the canonical link if is specified and if
     * is different of specified uri.
     *
     * @param bool $flag
     *
     * @return \Daric\Scraper
     */
    public function setFollowCanonical($flag = true)
    {
        $this->followCanonical = (bool) $flag;

        return $this;
    }

    /**
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Add an extractor.
     *
     * @param string             $name
     * @param ExtractorInterface $extractor
     *
     * @return \Daric\Scraper
     */
    public function addExtractor($name, ExtractorInterface $extractor)
    {
        $this->extractors[$name] = $extractor;

        return $this;
    }

    /**
     * @param array $extractors
     *
     * @return \Daric\Scraper
     */
    public function setExtractors(array $extractors)
    {
        foreach ($extractors as $name => $extractor) {
            $this->addExtractor($name, $extractor);
        }

        return $this;
    }

    /**
     * Retrieve all extractors.
     *
     * @retur array
     */
    public function getExtractors()
    {
        return $this->extractors;
    }

    /**
     * Run content extraction.
     *
     * @return array
     */
    public function extract()
    {
        foreach ($this->extractors as $name => $extractor) {
            $this->data[$name] = $extractor->extract($this->content);
        }

        return $this->data;
    }

    /**
     * @param string           $name
     * @param CleanerInterface $cleaner
     *
     * @return \Daric\Scraper
     */
    public function addCleaner($name, CleanerInterface $cleaner)
    {
        $this->cleaners[$name] = $cleaner;

        return $this;
    }

    /**
     * @param array $cleaners
     *
     * @return \Daric\Scraper
     */
    public function setCleaners(array $cleaners)
    {
        foreach ($cleaners as $name => $cleaner) {
            $this->addCleaner($name, $cleaner);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getCleaners()
    {
        return $this->cleaners;
    }

    /**
     * Performs cleaners on data.
     *
     * @return array
     */
    public function clean()
    {
        $before = $after = false;

        if (isset($this->cleaners['@before'])) {
            $before = $this->cleaners['@before'];
            unset($this->cleaners['@before']);
        }

        if (isset($this->cleaners['@after'])) {
            $after = $this->cleaners['@after'];
            unset($this->cleaners['@after']);
        }

        if ($before) {
            foreach ($this->data as $name => $value) {
                $this->data[$name] = $before->clean($value);
            }
        }

        foreach ($this->cleaners as $name => $cleaner) {
            if (isset($this->data[$name])) {
                $this->data[$name] = $cleaner->clean($this->data[$name]);
            }
        }

        if ($after) {
            foreach ($this->data as $name => $value) {
                $this->data[$name] = $after->clean($value);
            }
        }

        return $this->data;
    }

    public function addFormatter($name, FormatterInterface $formatter)
    {
        $this->formatters[$name] = $formatter;

        return $this;
    }

    public function setFormatters(array $formatters)
    {
        foreach ($formatters as $name => $formatter) {
            $this->addFormatter($name, $formatter);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getFormatters()
    {
        return $this->formatters;
    }

    /**
     * Performs formatter on data.
     *
     * @return array
     */
    public function format()
    {
        foreach ($this->formatters as $name => $formatter) {
            $value = null;
            if (isset($this->data[$name])) {
                $value = $this->data[$name];
            }

            $this->data[$name] = $formatter->format($value, $this->data);
        }

        return $this->data;
    }

    /**
     * @return \Daric\Document
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Absolutize $uri.
     *
     * @param string $uri
     *
     * @return string Absolutized uri
     */
    protected function absolutizeUri($uri)
    {
        $currentUriParts = \parse_url($this->getUri());

        foreach ([
            'query',
            'fragment',
        ] as $part) {
            if (isset($currentUriParts[$part])) {
                unset($currentUriParts[$part]);
            }
        }

        return http_build_url(
            $currentUriParts,
            $uri,
            HTTP_URL_REPLACE | HTTP_URL_STRIP_PORT
        );
    }
}
