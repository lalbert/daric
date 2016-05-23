<?php

namespace Daric\Cleaner;

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
        foreach ($cleaners as $cleaner) {
            $this->addCleaner($cleaner);
        }
    }

    /**
     * Add a cleaner to chain.
     *
     * @param CleanerInterface $cleaner
     *
     * @return \Daric\Cleaner\ChainCleaner
     */
    public function addCleaner(CleanerInterface $cleaner)
    {
        $this->cleaners[] = $cleaner;

        return $this;
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
