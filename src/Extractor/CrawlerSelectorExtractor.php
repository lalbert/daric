<?php

namespace Daric\Extractor;

use Symfony\Component\DomCrawler\Crawler;

class CrawlerSelectorExtractor implements ExtractorInterface
{
    protected $selector;

    public function __construct($selector)
    {
        $this->selector = $selector;
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

        return $content->filter($this->selector);
    }
}
