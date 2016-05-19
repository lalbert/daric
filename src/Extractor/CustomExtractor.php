<?php

namespace Daric\Extractor;

use Daric\ExtractorInterface;

class CustomExtractor implements ExtractorInterface
{
    protected $closure;

    public function __construct(\Closure $closure)
    {
        $this->closure = $closure;
    }

    public function extract($content)
    {
        $closure = $this->closure;

        return $closure($content);
    }
}
