<?php

namespace Daric\Cleaner;


/**
 * Strip whitespace (or other characters) from the end of a string.
 *
 * @author lalbert
 */
class RTrimCleaner extends TrimCleaner implements CleanerInterface
{
    protected function trim($value)
    {
        if (\is_null($this->charlist)) {
            return \rtrim($value);
        }

        return \rtrim($value, $this->charlist);
    }
}
