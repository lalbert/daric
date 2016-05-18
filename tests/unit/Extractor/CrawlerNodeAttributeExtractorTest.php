<?php

namespace Daric\Tests\Extractor;

use Daric\Extractor\CrawlerNodeAttributeExtractor;

class CrawlerNodeAttributeExtractorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @cover NodeAttributeExtractor::extract()
     */
    public function testNodeAttributeExtractorComplex()
    {
        $content = CrawlerCreator::customHtml('
            <img alt="One" />
            <img alt="Two" />
            <img alt="Three" />
            <img alt="Four" />
            <img alt="Five" />
        ');

        $extrator = new CrawlerNodeAttributeExtractor('alt');
        $this->assertEquals('One', $extrator->extract($content->filter('img')));

        $extrator = new CrawlerNodeAttributeExtractor('alt', 'last');
        $this->assertEquals('Five', $extrator->extract($content->filter('img')));

        $extrator = new CrawlerNodeAttributeExtractor('alt', 'index', 2);
        $this->assertEquals('Three', $extrator->extract($content->filter('img')));

        $extrator = new CrawlerNodeAttributeExtractor('alt', 'array');
        $this->assertArraySubset(['One', 'Two', 'Three', 'Four', 'Five'], $extrator->extract($content->filter('img')));
    }

    /**
     * @cover NodeAttributeExtractor::extract()
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage $content must be an instance of Symfony\Component\DomCrawler\Crawler
     */
    public function testString()
    {
        $extrator = new CrawlerNodeAttributeExtractor('alt');
        $extrator->extract('String');
    }

    /**
     * @cover NodeAttributeExtractor::extract()
     */
    public function testEmptyContent()
    {
        $extrator = new CrawlerNodeAttributeExtractor('alt');
        $content = CrawlerCreator::customHtml('<p>Content</p>');

        $this->assertNull($extrator->extract($content->filter('img')));
    }
}
