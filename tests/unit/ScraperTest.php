<?php

namespace Daric\Tests;

use Daric\Scraper;

class ScraperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @cover Scraper::__construct()
     * @cover Scraper::setUri()
     * @cover Scraper::getUri()
     * @cover Scraper::scrape()
     * @cover Scraper::getClient()
     * @cover Scraper::getClientConfig()
     * @cover Scraper::setFollowCanonical()
     * @cover Scraper::absolutizeUri()
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
}
