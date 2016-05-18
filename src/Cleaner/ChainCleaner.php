<?php

namespace Daric\Cleaner;

use Daric\CleanerInterface;

/**
 * Chain multiple cleaners.
 *
 * @author lalbert
 */
class ChainCleaner implements CleanerInterface
{
    protected $cleaners = [];

    /**
     * @param array $cleaners Array of Daric\CleanerInterface
     */
    public function __construct(array $cleaners)
    {
        $this->cleaners = $cleaners;
    }

    /**
     * Performs clean whith all registered cleaners
     * {@inheritdoc}
     *
     * @see \Daric\CleanerInterface::clean()
     */
    public function clean($content)
    {
        foreach ($this->cleaners as $cleaner) {
            $content = $cleaner->clean($content);
        }

        return $content;
    }
}
