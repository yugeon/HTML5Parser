<?php

namespace Yugeon\HTML5Parser;

/**
 * Represents html-node
 */
interface NodeAttributeInterface
{

    /**
     * Set attribute name.
     *
     * @param string $name
     * @return void
     */
    public function setName($name);

    /**
     * Getting an attribute name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set attribute value without quotes
     *
     * @param string|null $value If value is null, then attribute is empty
     * @return void
     */
    public function setValue($value);

    /**
     * Getting an attribute value without quotes
     *
     * @return string|null
     */
    public function getValue();

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
