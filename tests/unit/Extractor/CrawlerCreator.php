<?php

namespace Daric\Tests\Extractor;

use Symfony\Component\DomCrawler\Crawler;

class CrawlerCreator
{
    public static function simpleCrawler()
    {
        $dom = new \DOMDocument();
        $dom->loadHTML('
            <html>
              <head>
                <title>Test HTML</title>
                <meta name="description" content="HTML Test for scraper extrators." />
              </head>
              <body>
                <h1 id="h1">Page title</h1>
                <p class="first-p">First paragraph</p>
                <p class="second-p">Second paragraph</p>
              </body>
            </html>
        ');

        return new Crawler($dom);
    }

    public static function customHtml($html)
    {
        $dom = new \DOMDocument();
        $dom->loadHTML($html);

        return new Crawler($dom);
    }
}
