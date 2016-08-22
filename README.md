# Daric
Daric is a Simple and configurable PHP web spider and web scraper written under the [Goutte](https://github.com/FriendsOfPHP/Goutte) library.

## Installation

The best way to install Daric it use composer

	composer require lalbert/daric

## Usage

There are two components : `Scraper` and `Spider`.

### Scraper

`Scaper` is used to extract, clean, and format web page data.

It uses extractors, cleaners and formatters to achieve its goals.

```php
use Daric\Scraper;
use Daric\Extractor\CrawlerExtractorFactory;

$scraper = new Scrapper('http://website.tld');
$scraper->setExtractors([
  'meta_title' => CrawlerExtractorFactory::create('title@_text'), // get text node of <title></title>
  'meta_description' => CrawlerExtractorFactory::create('meta[name="description"]@content'), // get attribute "content" of <meta name="description" />
  'list' => CrawlerExtractorFactory::create('#content ul.list li@_text("array")') // get all text node of li item. Return an array
]);

$doc = $scraper->scrape(); // return Daric\Document

echo $doc->getData('meta_title');
print_r($doc['list']);
```

### Spider

`Spider` is used to crawl a website to scrape some web page data.

```php
use Daric\Spider;
use Daric\Scraper;
use Daric\Extractor\CrawlerExtractorFactory;

$spider = new Spider('http://website.tld');

$spider->setLinkExtractor(CrawlerExtractorFactory::create('#content article a.link@href("array")'));
$spider->setNextLinkExtractor(CrawlerExtractorFactory::create('#nav a.next@href'));

foreach ($spider as $pageUri) {
  $scraper = new Scraper($pageUri, $extractors, $cleaners, $formatters);
  $doc = $scraper->scrape();
  ...
}
```

## Licence

Daric is licensed under the MIT License - see the [LICENSE](LICENSE) file for details
