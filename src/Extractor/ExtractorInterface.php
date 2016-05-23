<?php

namespace Daric\Extractor;

interface ExtractorInterface
{
    /**
     * Extract the content of $content.
     *
     * @param $content
     */
    public function extract($content);
}
