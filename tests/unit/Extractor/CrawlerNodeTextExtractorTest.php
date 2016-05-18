<?php

namespace Daric\Tests\Extractor;

use Daric\Extractor\CrawlerNodeTextExtractor;

class CrawlerNodeTextExtractorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @cover NodeTextExtractor::extract()
     */
    public function testNodeTextExtractorComplex()
    {
        $content = CrawlerCreator::customHtml('
            <p>One</p>
            <p>Two</p>
            <p>Three</p>
            <p>Four</p>
            <p>Five</p>
        ');

        $extrator = new CrawlerNodeTextExtractor();
        $this->assertEquals('One', $extrator->extract($content->filter('p')));

        $extrator = new CrawlerNodeTextExtractor('last');
        $this->assertEquals('Five', $extrator->extract($content->filter('p')));

        $extrator = new CrawlerNodeTextExtractor('index', 2);
        $this->assertEquals('Three', $extrator->extract($content->filter('p')));

        $extrator = new CrawlerNodeTextExtractor('array');
        $this->assertArraySubset(['One', 'Two', 'Three', 'Four', 'Five'], $extrator->extract($content->filter('p')));
    }

    /**
     * @cover NodeTextExtractor::extract()
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage $content must be an instance of Symfony\Component\DomCrawler\Crawler
     */
    public function testString()
    {
        $extrator = new CrawlerNodeTextExtractor();
        $extrator->extract('String');
    }

    /**
     * @cover NodeTextExtractor::extract()
     */
    public function testEmptyContent()
    {
        $extrator = new CrawlerNodeTextExtractor();
        $content = CrawlerCreator::customHtml('<p>Content</p>');

        $this->assertNull($extrator->extract($content->filter('p.not')));
    }
}
