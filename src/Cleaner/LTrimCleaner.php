<?php

namespace Daric\Cleaner;

use Daric\CleanerInterface;

/**
 * Strip whitespace (or other characters) from the beginning of a string.
 *
 * @author lalbert
 */
class LTrimCleaner extends TrimCleaner implements CleanerInterface
{
    protected function trim($value)
    {
        if (\is_null($this->charlist)) {
            return \ltrim($value);
        }

        return \ltrim($value, $this->charlist);
    }
}
