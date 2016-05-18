<?php

namespace Daric\Tests\Extractor;

use Daric\Extractor\CrawlerNodeHtmlExtractor;

class CrawlerNodeHtmlExtractorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @cover NodeHtmlExtractor::extract()
     */
    public function testNodeHtmlExtractorComplex()
    {
        $content = CrawlerCreator::customHtml('
            <p><b>One</b></p>
            <p><b>Two</b></p>
            <p><b>Three</b></p>
            <p><b>Four</b></p>
            <p><b>Five</b></p>
        ');

        $extrator = new CrawlerNodeHtmlExtractor();
        $this->assertEquals('<b>One</b>', $extrator->extract($content->filter('p')));

        $extrator = new CrawlerNodeHtmlExtractor('last');
        $this->assertEquals('<b>Five</b>', $extrator->extract($content->filter('p')));

        $extrator = new CrawlerNodeHtmlExtractor('index', 2);
        $this->assertEquals('<b>Three</b>', $extrator->extract($content->filter('p')));

        $extrator = new CrawlerNodeHtmlExtractor('array');
        $this->assertArraySubset([
            '<b>One</b>',
            '<b>Two</b>',
            '<b>Three</b>',
            '<b>Four</b>',
            '<b>Five</b>',

        ],
            $extrator->extract($content->filter('p')));
    }

    /**
     * @cover NodeHtmlExtractor::extract()
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage $content must be an instance of Symfony\Component\DomCrawler\Crawler
     */
    public function testString()
    {
        $extrator = new CrawlerNodeHtmlExtractor();
        $extrator->extract('String');
    }

    /**
     * @cover NodeHtmlExtractor::extract()
     */
    public function testEmptyContent()
    {
        $extrator = new CrawlerNodeHtmlExtractor();
        $content = CrawlerCreator::customHtml('<p>Content</p>');

        $this->assertNull($extrator->extract($content->filter('img')));
    }
}
