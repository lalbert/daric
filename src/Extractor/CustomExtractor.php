<?php

namespace Daric\Extractor;

use Daric\ScraperInjectorInterface;
use Daric\Scraper;

class CustomExtractor implements ExtractorInterface, ScraperInjectorInterface
{
    protected $closure;
    protected $scraper;

    /**
     * {@inheritdoc}
     *
     * @see \Daric\ScraperInjectorInterface::setScraper()
     */
    public function setScraper(Scraper $scraper)
    {
        $this->scraper = $scraper;
    }

    public function __construct(\Closure $closure)
    {
        $this->closure = $closure;
    }

    public function extract($content)
    {
        $closure = $this->closure;

        return $closure($content, $this->scraper);
    }
}
