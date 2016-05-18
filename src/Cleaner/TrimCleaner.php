<?php

namespace Daric\Cleaner;

use Daric\CleanerInterface;

/**
 * Strip whitespace (or other characters) from the beginning and end of a
 * string.
 *
 * @see http://php.net/manual/en/function.trim.php
 *
 * @author lalbert
 */
class TrimCleaner implements CleanerInterface
{
    protected $charlist = null;

    /**
     * You can also specify the characters you want to strip, by means of the
     * character_mask parameter. Simply list all characters that you want to
     * be stripped. With .. you can specify a range of characters.
     
     *
     * @param string $charlist
     */
    public function __construct($charlist = null)
    {
        $this->charlist = $charlist;
    }

    public function clean($value)
    {
        $result = $value;

        if (\is_array($result)) {
            foreach ($result as $k => $v) {
                $result[$k] = $this->clean($v);
            }
        } else {
            $result = $this->trim($value);
        }

        return $result;
    }

    protected function trim($value)
    {
        if (\is_null($this->charlist)) {
            return \trim($value);
        }

        return \trim($value, $this->charlist);
    }
}
