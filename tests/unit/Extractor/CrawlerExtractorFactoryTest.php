<?php

namespace Daric\Tests\Extractor;

use Daric\Extractor\CrawlerSelectorExtractor;
use Daric\Extractor\CrawlerExtractorFactory;
use Daric\Extractor\ChainExtractor;
use Daric\Extractor\CrawlerNodeAttributeExtractor;
use Daric\Extractor\CrawlerNodeTextExtractor;
use Daric\Extractor\CrawlerNodeHtmlExtractor;

class CrawlerExtractorFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @cover CrawlerExtractorFactory::create
     * @cover ChainExtractor::addExtractor
     *
     * @dataProvider factoryProvider
     */
    public function testFactory($extractor, $expected)
    {
        $this->assertEquals($expected, $extractor);
    }

    public function factoryProvider()
    {
        return [
            '.selector' => [
                CrawlerExtractorFactory::create('.selector'),
                new CrawlerSelectorExtractor('.selector'),
            ],

            '.selector@attr' => [
                CrawlerExtractorFactory::create('.selector@attr'),
                new ChainExtractor([
                    new CrawlerSelectorExtractor('.selector'),
                    new CrawlerNodeAttributeExtractor('attr'),
                ]),
            ],

            '.selector@attr("array")' => [
                CrawlerExtractorFactory::create('.selector@attr("array")'),
                new ChainExtractor([
                    new CrawlerSelectorExtractor('.selector'),
                    new CrawlerNodeAttributeExtractor('attr', 'array'),
                ]),
            ],

            ".selector@attr('array')" => [
                CrawlerExtractorFactory::create(".selector@attr('array')"),
                new ChainExtractor([
                    new CrawlerSelectorExtractor('.selector'),
                    new CrawlerNodeAttributeExtractor('attr', 'array'),
                ]),
            ],

            '.selector@attr("array")' => [
                CrawlerExtractorFactory::create('.selector@attr("array")'),
                new ChainExtractor([
                    new CrawlerSelectorExtractor('.selector'),
                    new CrawlerNodeAttributeExtractor('attr', 'array'),
                ]),
            ],

            '.selector@_text("last")' => [
                CrawlerExtractorFactory::create('.selector@_text("last")'),
                new ChainExtractor([
                    new CrawlerSelectorExtractor('.selector'),
                    new CrawlerNodeTextExtractor('last'),
                ]),
            ],

            '.selector@_html("index", "5")' => [
                CrawlerExtractorFactory::create('.selector@_html("index", "5")'),
                new ChainExtractor([
                    new CrawlerSelectorExtractor('.selector'),
                    new CrawlerNodeHtmlExtractor('index', 5),
                ]),
            ],
        ];
    }
}
