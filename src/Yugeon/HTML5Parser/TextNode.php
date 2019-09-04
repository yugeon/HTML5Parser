<?php

namespace Yugeon\HTML5Parser;

class TextNode extends \DOMText implements NodeInterface
{

    public function __construct($value = '', $doEncode = true)
    {
        if ($doEncode) {
            $value = $this->htmlDecode($value);
            $value = $this->htmlEncode($value);
        }

        parent::__construct($value);
    }

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

    protected function htmlDecode($string) {
        return html_entity_decode($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    protected function htmlEncode($string)
    {
        $string = htmlentities($string, ENT_QUOTES | ENT_XML1, 'UTF-8');
        $string = mb_convert_encoding($string, 'HTML-ENTITIES', 'UTF-8');
        return $string;
    }
}
