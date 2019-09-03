<?php

namespace Yugeon\HTML5Parser;

class CommentNode extends \DOMComment implements NodeInterface
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
        return true;
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
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function isSelfClosingTag()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getHtml()
    {
        return "<!--{$this->textContent}-->";
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
