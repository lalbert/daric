<?php

namespace Daric\Formatter;

class DateTimeFormatter implements FormatterInterface
{
    protected $formatIn;
    protected $formatOut;

    public function __construct($formatIn, $formatOut)
    {
        $this->formatIn = $formatIn;
        $this->formatOut = $formatOut;
    }

    public function format($value)
    {
        if (\is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = $this->format($v);
            }
        } else {
            $date = \DateTime::createFromFormat($this->formatIn, $value);
            $value = $date->format($this->formatOut);
        }

        return $value;
    }
}
