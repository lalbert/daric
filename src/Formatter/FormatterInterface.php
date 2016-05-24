<?php

namespace Daric\Formatter;

interface FormatterInterface
{
    /**
     * Format value $value.
     *
     * @param string|array|null $value
     */
    public function format($value);
}
