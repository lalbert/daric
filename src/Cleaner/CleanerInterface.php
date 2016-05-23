<?php

namespace Daric\Cleaner;

interface CleanerInterface
{
    /**
     * Clean $value.
     *
     * @param $value
     */
    public function clean($value);
}
