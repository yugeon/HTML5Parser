<?php

namespace Yugeon\HTML5Parser;

/**
 * Represetn HTML Node
 */
interface NodeInterface
{
    /**
     * True if current node is text node.
     *
     * @return boolean
     */
    public function isTextNode();

    /**
     * True if current node is comment.
     *
     * @return boolean
     */
    public function isComment();

    /**
     * True if current node is element.
     *
     * @return boolean
     */
    public function isElement();

    /**
     * True if current node is doctype.
     *
     * @return boolean
     */
    public function isDoctype();

    /**
     * True if current node is self closing tag.
     *
     * @return boolean
     */
    public function isSelfClosingTag();

    /**
     * Getting outer html.
     *
     * @return string
     */
    public function getHtml();

    /**
     * Getting inner html.
     *
     * @return string
     */
    public function getInnerHtml();

}
