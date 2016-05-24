<?php

namespace Daric\Formatter;

class ChainFormatter implements FormatterInterface
{
    protected $formatters = [];

    public function __construct(array $formatters)
    {
        foreach ($formatters as $formatter) {
            $this->addFormatter($formatter);
        }
    }

    /**
     * Add a formatter to chain.
     *
     * @param FormatterInterface $formatter
     *
     * @return \Daric\Formatter\ChainFormatter
     */
    public function addFormatter(FormatterInterface $formatter)
    {
        $this->formatters[] = $formatter;

        return $this;
    }

    public function format($value)
    {
        foreach ($this->formatters as $formatter) {
            $value = $formatter->format($value);
        }

        return $value;
    }
}
