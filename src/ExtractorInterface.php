<?php

namespace Daric;

interface ExtractorInterface
{
    /**
     * Extract the content of $content.
     *
     * @param $content
     */
    public function extract($content);
}
