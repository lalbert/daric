<?php

namespace Daric\Extractor;

/**
 * Chain multiple extractors.
 *
 * @author lalbert
 */
class ChainExtractor implements ExtractorInterface
{
    protected $extractors = [];

    /**
     * @param array $extractors Array of Daric\ExtractorInterface
     */
    public function __construct(array $extractors)
    {
        foreach ($extractors as $extractor) {
            $this->addExtractor($extractor);
        }
    }

    /**
     * Add an extractor to chain.
     *
     * @param ExtractorInterface $extractor
     *
     * @return \Daric\Extractor\ChainExtractor
     */
    public function addExtractor(ExtractorInterface $extractor)
    {
        $this->extractors[] = $extractor;

        return $this;
    }

    /**
     * Performs extraction with all registered extractors
     * {@inheritdoc}
     *
     * @see \Daric\ExtractorInterface::extract()
     */
    public function extract($content)
    {
        foreach ($this->extractors as $extractor) {
            $content = $extractor->extract($content);
        }

        return $content;
    }
}
