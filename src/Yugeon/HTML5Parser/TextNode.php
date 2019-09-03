<?php

namespace Yugeon\HTML5Parser;

class TextNode extends \DOMText implements NodeInterface
{

    /**
     * {@inheritDoc}
     */
    public function isDoctype()
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function isComment()
    {
        return false;
    }
    /**
     * {@inheritDoc}
     */
    public function isElement()
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function isTextNode()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isSelfClosingTag()
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getHtml()
    {
        return $this->wholeText;
    }

    /**
     * {@inheritDoc}
     */
    public function getInnerHtml()
    {
        return '';
    }

    public function __toString()
    {
        return $this->getHtml();
    }
}
