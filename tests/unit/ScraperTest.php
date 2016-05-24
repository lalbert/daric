<?php

namespace Daric\Tests;

use Daric\Scraper;
use Goutte\Client;
use Daric\Extractor\CrawlerExtractorFactory;
use Daric\Cleaner\TrimCleaner;
use Daric\Formatter\FloatFormatter;
use Daric\Extractor\ChainExtractor;
use Daric\Extractor\CrawlerSelectorExtractor;
use Daric\Extractor\CrawlerNextExtractor;
use Daric\Extractor\CrawlerNodeTextExtractor;
use Daric\Cleaner\LTrimCleaner;

class ScraperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @cover Scraper::__construct
     * @cover Scraper::setUri
     * @cover Scraper::getUri
     * @cover Scraper::scrape
     * @cover Scraper::getClient
     * @cover Scraper::getClientConfig
     * @cover Scraper::setFollowCanonical
     * @cover Scraper::absolutizeUri
     */
    public function testCanonical()
    {
        $uri = 'http://php.net/header';

        $scraper = new Scraper($uri);
        $scraper->scrape();
        $this->assertEquals($uri, $scraper->getUri());

        $scraper = new Scraper($uri);
        $scraper->setFollowCanonical(true);
        $scraper->scrape();
        $this->assertNotEquals($uri, $scraper->getUri());
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage You must set uri before run scrape.
     */
    public function testNoUri()
    {
        $scraper = new Scraper();
        $scraper->scrape();
    }

    /**
     * @cover Scraper::getContent
     */
    public function testGetContent()
    {
        $scraper = new Scraper('https://httpbin.org/');
        $this->assertInstanceOf('Symfony\\Component\\DomCrawler\\Crawler', $scraper->getContent());
    }

    /**
     * @cover Scraper::setClient
     * @cover Scraper::getContent
     */
    public function testSetClient()
    {
        $client = new Client();
        $scraper = new Scraper('https://httpbin.org/');
        $scraper->setClient($client);

        $content = $scraper->getContent();
        $this->assertInstanceOf('Symfony\\Component\\DomCrawler\\Crawler', $content);

        $h1 = $content->filter('h1')->first()->text();
        $this->assertContains('HTTP Request & Response Service', $h1);
    }

    /**
     * @cover Scraper:addExtractor
     * @cover Scraper:setExtractors
     * @cover Scraper:getExtractors
     */
    public function testExtractor()
    {
        $scraper = new Scraper('https://httpbin.org/');
        $scraper->setExtractors([CrawlerExtractorFactory::create('a@href("array")')]);

        foreach ($scraper->getExtractors() as $extractor) {
            $this->assertInstanceOf('Daric\\Extractor\\ExtractorInterface', $extractor);
        }
    }

    /**
     * @cover Scraper:addCleaner
     * @cover Scraper:setCleaners
     * @cover Scraper:getCleaners
     */
    public function testCleaner()
    {
        $scraper = new Scraper('https://httpbin.org/');
        $scraper->setCleaners([new TrimCleaner()]);

        foreach ($scraper->getCleaners() as $cleaner) {
            $this->assertInstanceOf('Daric\\Cleaner\\CleanerInterface', $cleaner);
        }
    }

    /**
     * @cover Scraper:addFormatter
     * @cover Scraper:setFormatters
     * @cover Scraper:getFormatters
     */
    public function testFormatter()
    {
        $scraper = new Scraper('https://httpbin.org/');
        $scraper->setFormatters([new FloatFormatter()]);

        foreach ($scraper->getFormatters() as $formatter) {
            $this->assertInstanceOf('Daric\\Formatter\\FormatterInterface', $formatter);
        }
    }

    public function testFullScrape()
    {
        $scraper = new Scraper('https://httpbin.org/');
        $scraper->setExtractors([
            'title' => CrawlerExtractorFactory::create('head title@_text'),
            'h1' => CrawlerExtractorFactory::create('h1@_text'),
            //'links' => CrawlerExtractorFactory::create('a@href("array")'),
            'first_endpoint' => new ChainExtractor([
                new CrawlerSelectorExtractor('h2#ENDPOINTS'),
                new CrawlerNextExtractor(),
                new CrawlerSelectorExtractor('li'),
                new CrawlerNodeTextExtractor(),
            ]),
            'first_endpoint_raw' => new ChainExtractor([
                new CrawlerSelectorExtractor('h2#ENDPOINTS'),
                new CrawlerNextExtractor(),
                new CrawlerSelectorExtractor('li'),
                new CrawlerNodeTextExtractor(),
            ]),
        ]);
        $scraper->setCleaners([
            '@before' => new TrimCleaner(),
            '@after' => new TrimCleaner(),
            'first_endpoint' => new LTrimCleaner('/'),
        ]);

        $doc = $scraper->scrape();

        $this->assertArrayHasKey('title', $doc);
        $this->assertEquals($doc, $scraper->getDocument());
        $this->assertEquals($doc->getData(), $scraper->getData());
        $this->assertNotEquals($doc['first_endpoint'], $doc['first_endpoint_raw']);
    }
}
