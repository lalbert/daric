<?php

namespace Daric\Extractor;

/**
 * CrawlerExtractorFactory is a shorhand to create complex chain extrator for
 * crawler.
 *
 * Examples :
 *
 * <b>Simple selector</b>
 *
 * CrawlerExtractorFactory::create('.selector');
 *
 * => new CrawlerSelectorExtractor('.selector')
 *
 * <b>Get simple attribute</b>
 *
 * CrawlerExtractorFactory::create('.selector@attr');
 *
 * => new ChainExtractor([
 *   new CrawlerSelectorExtractor('.selector'),
 *   new CrawlerNodeAttributeExtractor('attr')
 * ])
 *
 * <b>Get attribute with params</b>
 *
 * CrawlerExtractorFactory::create('.selector@attr("index", 5)');
 *
 * => new ChainExtractor([
 *   new CrawlerSelectorExtractor('.selector'),
 *   new CrawlerNodeAttributeExtractor('attr', 'index', 5)
 * ])
 *
 * <b>get text</b>
 *
 * CrawlerExtractorFactory::create('.selector@_text("array")');
 *
 * => new ChainExtractor([
 *   new CrawlerSelectorExtractor('.selector'),
 *   new CrawlerNodeTextExtractor('array')
 * ])
 *
 * <b>get html</b>
 *
 * CrawlerExtractorFactory::create('.selector@_html("array")');
 *
 * => new ChainExtractor([
 *   new CrawlerSelectorExtractor('.selector'),
 *   new CrawlerNodeHtmlExtractor('array')
 * ])
 *
 * @author lalbert
 */
class CrawlerExtractorFactory
{
    /**
     * @param string $factory
     */
    public static function create($factory)
    {
        if (false !== (strpos($factory, '@'))) {
            list($selector, $action) = explode('@', $factory);
            $extractor = new ChainExtractor([
                new CrawlerSelectorExtractor($selector),
            ]);

            $args = [];
            if (false !== (\strpos($action, '('))) {
                $pattern = '#\((.*)\)$#';
                if (\preg_match($pattern, $action, $_args)) {
                    $args = explode(',', $_args[1]);
                }

                $action = \preg_replace($pattern, '', $action);
            }

            // Clean arguments
            $args = \array_map(function ($value) {
                $value = trim($value);
                $value = trim($value, '"\'');

                return $value;
            }, $args);

            $matchStrategy = isset($args[0]) ? $args[0] : 'first';
            $matchIndex = isset($args[1]) ?   $args[1]  : 0;

            switch ($action) {
                case '_text':
                    $extractor->addExtractor(new CrawlerNodeTextExtractor($matchStrategy, $matchIndex));
                    break;
                case '_html':
                    $extractor->addExtractor(new CrawlerNodeHtmlExtractor($matchStrategy, $matchIndex));
                    break;
                default:
                    $extractor->addExtractor(new CrawlerNodeAttributeExtractor($action, $matchStrategy, $matchIndex));
                    break;
            }

            return $extractor;
        } else {
            return new CrawlerSelectorExtractor($factory);
        }
    }
}
