<?php

namespace Daric\Extractor;

use Daric\ExtractorInterface;
use Symfony\Component\DomCrawler\Crawler;

class CrawlerNodeHtmlExtractor implements ExtractorInterface
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

    /**
     * {@inheritdoc}
     *
     * @see \Daric\ExtractorInterface::extract()
     */
    public function extract($content)
    {
        if (!($content instanceof Crawler)) {
            throw new \InvalidArgumentException('$content must be an instance of Symfony\Component\DomCrawler\Crawler');
        }

        if (!count($content)) {
            return;
        }

        switch ($this->matchStrategy) {
            case 'first':
                return $content->first()->html();
            break;
            case 'last':
                return $content->last()->html();
            break;
            case 'index':
                return $content->eq($this->matchIndex)->html();
            break;
            case 'array':
                $result = [];
                $content->each(function ($node) use (&$result) {
                    if (count($node)) {
                        $result[] = $node->html();
                    }
                });

                return $result;
            break;
        }

        return;
    }
}
