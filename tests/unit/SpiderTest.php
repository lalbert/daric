<?php

namespace Daric\Tests;

use Daric\Spider;
use Daric\Extractor\CrawlerExtractorFactory;

class SpiderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @cover Spider::spide
     *
     * @return \Daric\Spider
     */
    public function testSpide()
    {
        $s = new Spider('http://httpbin.org/links/10');
        $s->setLinkExtractor(CrawlerExtractorFactory::create('a@href("array")'));
        $links = $s->spide();

        $this->assertCount(9, $links);

        return $s;
    }

    /**
     * @cover Spider::setLimit
     * @cover Spider::valid
     * @cover Spider::addLink
     */
    public function testLimit()
    {
        $s = new Spider('http://httpbin.org/links/10');
        $s->setLinkExtractor(CrawlerExtractorFactory::create('a@href("array")'));
        $s->setLimit(5);

        $links = $s->spide();

        $this->assertCount(5, $links);
    }

    /**
     * @cover Spider::current
     * @cover Spider::next
     * @cover Spider::key
     * @cover Spider::valid
     * @cover Spider::rewind
     */
    public function testIterator()
    {
        $s = new Spider('http://httpbin.org/links/10');
        $s->setLinkExtractor(CrawlerExtractorFactory::create('a@href("array")'));

        foreach ($s as $link) {
            $this->assertStringStartsWith('http://httpbin.org/links/10', $link);
        }
    }

    /**
     * @depends testSpide
     * @cocer Spider::Count
     *
     * @param Spider $spider
     */
    public function testCount(Spider $spider)
    {
        $this->assertEquals(9, count($spider));
    }
}
