<?php

namespace Daric\Extractor;

use Daric\ExtractorInterface;

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
        $this->extractors = $extractors;
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
