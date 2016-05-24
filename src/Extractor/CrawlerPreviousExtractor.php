<?php

namespace Daric\Extractor;

use Symfony\Component\DomCrawler\Crawler;

class CrawlerPreviousExtractor implements ExtractorInterface
{
    /**
     * @var string
     */
    protected $matchStrategy = 'first';

    /**
     * @var int
     */
    protected $matchIndex = 0;

    /**
     * @param string $matchStrategy
     * @param number $matchIndex
     */
    public function __construct($matchStrategy = 'first', $matchIndex = 0)
    {
        $this->matchStrategy = $matchStrategy;
        $this->matchIndex = $matchIndex;
    }

    public function extract($content)
    {
        if (!($content instanceof Crawler)) {
            throw new \InvalidArgumentException('$content must be an instance of Symfony\Component\DomCrawler\Crawler');
        }

        if (!count($content)) {
            return;
        }

        $next = $content->previousAll();

        return $next;
    }
}
