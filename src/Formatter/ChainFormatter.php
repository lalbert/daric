<?php

namespace Daric\Formatter;

use Daric\FormatterInterface;

class ChainFormatter implements FormatterInterface
{
    protected $formatters = [];

    public function __construct(array $formatters)
    {
        $this->formatters = $formatters;
    }

    public function format($value, $data)
    {
        foreach ($this->formatters as $formatter) {
            $value = $formatter->format($value, $data);
        }

        return $value;
    }
}
