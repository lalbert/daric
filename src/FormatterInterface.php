<?php

namespace Daric;

interface FormatterInterface
{
    /**
     * Format value $value.
     *
     * @param string|array|null $value
     * @param array             $data  All scraped data
     */
    public function format($value, $data);
}
