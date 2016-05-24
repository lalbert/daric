<?php

namespace Daric\Extractor;

class RegexExtractor implements ExtractorInterface
{
    protected $pattern;

    public function __construct($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Daric\Extractor\ExtractorInterface::extract()
     */
    public function extract($content)
    {
        $value = null;
        if (\is_array($content)) {
            foreach ($content as $v) {
                $value[] = $this->extract($v);
            }
        } else {
            if (false !== \preg_match($this->pattern, $content, $matches)) {
                $value = $matches[1];
            }
        }

        return $value;
    }
}
