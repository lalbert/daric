<?php

namespace Daric\Tests\Extractor;

use Daric\Extractor\CrawlerSelectorExtractor;

class CrawlerSelectorExtractorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @cover CrawlerSelectorExtractor::__construct()
     * @cover CrawlerSelectorExtractor::extract()
     */
    public function testSelector()
    {
        $content = CrawlerCreator::simpleCrawler();

        $extractor = new CrawlerSelectorExtractor('p');
        $result = $extractor->extract($content);
        $this->assertInstanceOf('Symfony\\Component\\DomCrawler\\Crawler', $result);
        $this->assertEquals(2, count($result));
    }

    /**
     * @cover CrawlerSelectorExtractor::extract()
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage $content must be an instance of Symfony\Component\DomCrawler\Crawler
     */
    public function testString()
    {
        $extrator = new CrawlerSelectorExtractor('p');
        $extrator->extract('String');
    }
}
