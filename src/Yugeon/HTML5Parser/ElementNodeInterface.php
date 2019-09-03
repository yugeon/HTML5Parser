<?php

namespace Yugeon\HTML5Parser;

/**
 * Represetn HTML Element Node
 */
interface ElementNodeInterface
{

    /**
     * Add end tag for this node.
     *
     * @param string $endTag
     * @return void
     */
    public function addEndTag($endTag);

    /**
     * Getting end tag for this node.
     *
     * @return string
     */
    public function getEndTag();

    /**
     * Add a new attribute to the current tag or replace an existing.
     * @see NodeAttribute::__construct.
     *
     * @param string $name
     * @param string $value
     * @param string $whitespaceBefore
     * @param string $signStr
     * @param string $quotesSymbol
     * @return \DOMAttr
     */
    public function setAttribute($name, $value, $whitespaceBefore = '', $signStr = null, $quotesSymbol = '');

    /**
     * Set whitespaces after the tag name if there are no attributes
     *
     * @param string $ws
     * @return void
     */
    public function setWhitespaces($ws);

}
