<?php

namespace Daric\Formatter;


class ExplodeFormatter implements FormatterInterface
{
    protected $delimiter;
    protected $limit;
    protected $map = [];

    public function __construct($delimiter, array $map = [], $limit = PHP_INT_MAX)
    {
        $this->delimiter = $delimiter;
        $this->map = $map;
        $this->limit = $limit;
    }

    public function format($value, $data)
    {
        $result = $value;

        if (\is_array($result)) {
            foreach ($result as $k => $v) {
                $result[$k] = $this->format($v, $data);
            }
        } else {
            $arr = \explode($this->delimiter, $result, $this->limit);
            if (!empty($this->map)) {
                foreach ($this->map as $index => $key) {
                    if (isset($arr[$index])) {
                        $result[$key] = $arr[$index];
                    }
                }
            } else {
                $result = $arr;
            }
        }

        return $result;
    }
}
