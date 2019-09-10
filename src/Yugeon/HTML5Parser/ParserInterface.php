<?php

namespace Yugeon\HTML5Parser;

interface ParserInterface {

    /**
     * Clear the parser for reuse.
     *
     * @return void
     */
    public function clear();

    /**
     * Parse input html.
     *
     * @param string $html Input html.
     * @return \Yugeon\HTML5Parser\Dom\DomDocument
     */
    public function parse($html);

    /**
     * The only way to get the correct HTML.
     *
     * @return string
     */
    public function getHtml();

    /**
     * Reference to the DomDocument object of the current parsing result.
     * Note that it is better to use the getHtml() method instead saveHtml().
     *  @see DomDocument::getHtml()
     *
     * @return \Yugeon\HTML5Parser\Dom\DomDocument
     */
    public function getDomDocument();

    /**
     * Enable or disable autoescaping special chars in text nodes.
     *
     * @param bool $isAutoescape
     * @return void
     */
    public function setAutoescapeTextNodes($isAutoescape);

    /**
     * Getting current setting for autoescaping special chars in text nodes.
     *
     * @return bool
     */
    public function getAutoescapeTextNodes();
}