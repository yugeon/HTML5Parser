<?php

namespace Yugeon\HTML5Parser\Dom;

/**
 * Represents html-node
 */
interface NodeAttributeInterface
{

    /**
     * Getting an attribute name.
     *
     * @return string
     */
    public function getName();

    /**
     * Getting an attribute value without quotes
     *
     * @return string|null
     */
    public function getValue();

    /**
     * Set an attribute value without quotes
     *
     * @param string $value
     * @param bool $doEncode Autoescape value
     * @return void
     */
    public function setValue($value, $doEncode = false);

    /**
     * Set whitespace before attriubte name.
     *
     * @param string $ws
     * @return void
     */
    public function setPreservedWhitespace($ws);

    /**
     * Getting whitespace before attriubte name.
     *
     * @return string
     */
    public function getPreservedWhitespace();

    /**
     * Set equal sign and whitespaces around it.
     * @example ' = '
     *
     * @param string|null $signStr
     * @return void
     */
    public function setSignStr($signStr);

    /**
     *
     * @return string|null
     */
    public function getSignStr();

    /**
     * Set quotes symbol which use around an attribute value. Empty value - no quotes.
     *
     * @param string $quotesSymbol
     * @return void
     */
    public function setQuotesSymbol($quotesSymbol);

    /**
     * Getting quotes symbol.
     *
     * @return string
     */
    public function getQuotesSymbol();

    /**
     * Generate an attribute html.
     *
     * @return string
     */
    public function getHtml();
}
