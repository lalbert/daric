<?php

namespace Daric;

use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use Symfony\Component\DomCrawler\Link;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Spider is not intended to crawl all the links from a website, but only to
 * collect a specific link list in a list of results, eventually paginated.
 *
 * @author lalbert
 */
class Spider implements \Countable, \Iterator
{
    /**
     * @var string Base uri to spide
     */
    protected $uri;

    /**
     * @var array List of uri spided
     */
    protected $links = [];

    /**
     * @var Daric\ExtractorInterface
     */
    protected $linkExtractor;

    /**
     * @var Daric\ExtractorInterface
     */
    protected $nextLinkExtractor;

    /**
     * @var GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * @var array
     */
    protected $clientConfig;

    /**
     * @var Symfony\Component\DomCrawler\Crawler
     */
    protected $content;

    /**
     * @var number
     */
    protected $count;

    /**
     * @var Daric\ExtractorInterface
     */
    protected $countResultsExtractor;

    /**
     * Limit links recolt. If $limit = -1 it's illimited.
     *
     * @var number.
     */
    protected $limit = -1;

    /**
     * Index for \Iterator.
     *
     * @var number
     */
    private $index = 0;

    public function __construct($uri = null)
    {
        if (!\is_null($uri)) {
            $this->setUri($uri);
        }
    }

    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function spide($uri = null)
    {
        if (!\is_null($uri)) {
            $this->setUri($uri);
        }

        if (!$this->getUri()) {
            throw new \InvalidArgumentException('You must set uri before run scrape.');
        }

        $this->content = $this->getClient()->request('GET', $this->getUri());
        $this->extractLinks();

        return $this;
    }

    /**
     * Extract links in current content.
     */
    protected function extractLinks()
    {
        $links = $this->linkExtractor->extract($this->content);
        if (!\is_array($links)) {
            throw new \InvalidArgumentException('linkExtractor must return an array.');
        }

        foreach ($links as $link) {
            $this->addLink($link);
        }
    }

    /**
     * @param string $link
     *
     * @return \Daric\Spider
     */
    public function addLink($link)
    {
        if ($this->limit > -1 && count($this->links) >= $this->limit) {
            return $this;
        }

        $link = $this->prepareLink($link);

        if (!\in_array($link, $this->links)) {
            \array_push($this->links, $link);
        }

        return $this;
    }

    /**
     * @param string $href
     */
    protected function prepareLink($href)
    {
        $crawler = new Crawler("<html><body><a href='$href'></a></body></html>", $this->getUri());
        $link = $crawler->filter('a')->link();

        return $link->getUri();
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
     * Set link extractor.
     * Extractor must return an array of string, and string must be an uri.
     *
     * @param ExtractorInterface $linkExtractor
     *
     * @return Daric\Spider
     */
    public function setLinkExtractor(ExtractorInterface $linkExtractor)
    {
        $this->linkExtractor = $linkExtractor;

        return $this;
    }

    /**
     * @return the ExtractorInterface
     */
    public function getNextLinkExtractor()
    {
        return $this->nextLinkExtractor;
    }

    /**
     * @param Daric\ExtractorInterface $nextLinkExtractor
     */
    public function setNextLinkExtractor(ExtractorInterface $nextLinkExtractor)
    {
        $this->nextLinkExtractor = $nextLinkExtractor;

        return $this;
    }

    /**
     * Implements \Countable::count().
     *
     * Retrieve the number of link to spide. If countResultsExtractor is set
     * will return this value instead of count($links[]).
     *
     * Example :
     *
     * The spided page contain a bloc with result information :
     *
     *  <div id="results">
     *    Results <span id="result-start">1</span> to <span id="result-end">10</span> from <span id="results-total">100</span>
     *  </div>
     *
     * Set CountResultsExtractor with :
     *
     * new \Daric\Extrator\ChainExtractor([
     *   \Daric\Extractor\CrawlerSelectorExtractor('#results-total'),
     *   \Daric\Extractor\CrawlerNodeTextExtractor()
     * ]);
     *
     *
     *
     * @return number
     */
    public function count()
    {
        if ($this->countResultsExtractor) {
            if (!$this->count) {
                $this->count = (int) $this->countResultsExtractor->extract($this->content);
            }

            return $this->count;
        }

        return $this->currentCount();
    }

    /**
     * @return the ExtractorInterface
     */
    public function getCountResultsExtractor()
    {
        return $this->countResultsExtractor;
    }

    /**
     * @param Daric\ExtractorInterface $countResultsExtractor
     */
    public function setCountResultsExtractor(ExtractorInterface $countResultsExtractor)
    {
        $this->countResultsExtractor = $countResultsExtractor;

        return $this;
    }

    public function hasNextPage()
    {
        if (!$this->nextLinkExtractor) {
            return false;
        }

        return !\is_null($this->nextLinkExtractor->extract($this->content));
    }

    public function getNextPage()
    {
        return $this->nextLinkExtractor->extract($this->content);
    }

    /**
     * Return the current size of $links[].
     *
     * @return number
     */
    final public function currentCount()
    {
        return count($this->links);
    }

    /**
     * Implements Iterator::current()
     * {@inheritdoc}
     *
     * @see Iterator::current()
     */
    public function current()
    {
        return $this->links[$this->index];
    }

    /**
     * Implements Iterator::next()
     * {@inheritdoc}
     *
     * @see Iterator::next()
     */
    public function next()
    {
        ++$this->index;
    }

    /**
     * Implements Iterator::key()
     * {@inheritdoc}
     *
     * @see Iterator::key()
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * Implements Iterator::valid()
     * {@inheritdoc}
     *
     * @see Iterator::valid()
     */
    public function valid()
    {
        if (isset($this->links[$this->index])) {
            return true;
        }

        if ($this->limit > -1 && count($this->links) >= $this->limit) {
            return false;
        }

        if ($this->hasNextPage()) {
            $this->spide($this->getNextPage());
        }

        return isset($this->links[$this->index]);
    }

    /**
     * Implements Iterator::rewind()
     * {@inheritdoc}
     *
     * @see Iterator::rewind()
     */
    public function rewind()
    {
        $this->index = 0;
        if (!$this->content) {
            $this->spide();
        }
    }

    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Limit links recolt.
     *
     * @param int $limit
     *
     * @return \Daric\Spider
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }
}
