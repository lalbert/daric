<?php

namespace Daric\Formatter;

class FloatFormatter implements FormatterInterface
{
    public function format($value)
    {
        $result = $value;
        if (\is_array($result)) {
            foreach ($result as $k => $v) {
                $result[$k] = $this->format($v);
            }
        } else {
            $dotCount = \substr_count($result, '.');
            $commaCount = \substr_count($result, ',');

            if (1 == $commaCount && 0 == $dotCount) {
                $result = \str_replace(',', '.', $result);
            }

            $result = filter_var($result, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        }

        return $result;
    }
}
