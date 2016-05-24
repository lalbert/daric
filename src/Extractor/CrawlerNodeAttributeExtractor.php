<?php

namespace Daric\Extractor;

use Symfony\Component\DomCrawler\Crawler;

/**
 * NodeAttribute allow to extract attribute of content. Content must be an
 * instance of Symfony\Component\DomCrawler\Crawler.
 *
 * @author lalbert
 */
class CrawlerNodeAttributeExtractor implements ExtractorInterface
{
    /**
     * @var string
     */
    protected $attribute;

    /**
     * @var string
     */
    protected $matchStrategy = 'first';

    /**
     * @var int
     */
    protected $matchIndex = 0;

    /**
     * @param string $attribute     Attribute of node to extract.
     * @param string $matchStrategy
     * @param number $matchIndex
     */
    public function __construct($attribute, $matchStrategy = 'first', $matchIndex = 0)
    {
        $this->attribute = $attribute;
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
                return $content->first()->attr($this->attribute);
            break;
            case 'last':
                return $content->last()->attr($this->attribute);
            break;
            case 'index':
                return $content->eq($this->matchIndex)->attr($this->attribute);
            break;
            case 'array':
                $result = [];
                $content->each(function (Crawler $node) use (&$result) {
                    if (count($node)) {
                        $result[] = $node->attr($this->attribute);
                    }
                });

                return $result;
            break;
        }

        return;
    }
}
