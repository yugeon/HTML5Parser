<?php

namespace Yugeon\HTML5Parser\Dom;

class NodeAttribute extends \DOMAttr implements NodeAttributeInterface
{
    /**
     * Track whether this is an empty attribute.
     *
     * @var string|null
     */
    protected $origValue = null;

    /**
     * Whitespaces before attribute name.
     *
     * @var string
     */
    protected $preservedWhitespace = '';

    /**
     * Equal sign and whitespaces around it.
     *
     * @var string|null
     */
    protected $signStr = null;

    /**
     * Quotes symbol around attribute value. Default - no quotes.
     *
     * @var string
     */
    protected $quotesSymbol = '';

    /**
     * Create NodeAttributeInterface instance
     *
     * @param string $name Name of attribute
     * @param string|null $value Value of attribute, if null - attribute is empty
     * @param string $whitespaceBefore Whitespaces before attribute name
     * @param string|null $signStr Equal sign and whitespaces around it
     * @param string $quotesSymbol Quotes symbol around attribute value. Default - double quotes.
     * @param string $doEncode Autoescape values.
     */
    public function __construct($name, $value = null, $whitespaceBefore = '', $signStr = null, $quotesSymbol = '"', $doEncode = false)
    {

        $this->origValue = $value;

        $this->preservedWhitespace = $whitespaceBefore;

        $this->setSignStr($signStr);

        $this->setQuotesSymbol($quotesSymbol);

        if ($doEncode) {
            $value = $this->htmlDecode($value);
            $value = $this->htmlEncode($value);
        }

        parent::__construct($name, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritDoc}
     */
    public function setValue($value, $doEncode = false)
    {
        if ($doEncode) {
            $value = $this->htmlDecode($value);
            $value = $this->htmlEncode($value);
        }

        $this->value = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function setPreservedWhitespace($ws)
    {
        $this->preservedWhitespace = $ws;
    }

    /**
     * {@inheritDoc}
     */
    public function getPreservedWhitespace()
    {
        return $this->preservedWhitespace;
    }

    /**
     * {@inheritDoc}
     */
    public function setSignStr($signStr)
    {
        $this->signStr = $signStr;
    }

    /**
     * {@inheritDoc}
     */
    public function getSignStr()
    {
        return $this->signStr;
    }

    /**
     * {@inheritDoc}
     */
    public function setQuotesSymbol($quotesSymbol)
    {
        if (empty($quotesSymbol)) {
            $this->quotesSymbol = '';
            return;
        }

        $allowedQuotes = ['"', "'"];
        if (in_array($quotesSymbol, $allowedQuotes, true)) {
            $this->quotesSymbol = $quotesSymbol;
        } else {
            $this->quotesSymbol = '';
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getQuotesSymbol()
    {
        return $this->quotesSymbol;
    }

    /**
     * {@inheritDoc}
     */
    public function getHtml()
    {
        $html = $this->preservedWhitespace;
        if (!is_null($this->origValue)) {
            $html .= $this->getName() . (!is_null($this->signStr) ? $this->signStr : '=') . $this->quotesSymbol . $this->getValue() . $this->quotesSymbol;
        } else {
            $html .= $this->getName() . (!is_null($this->signStr) ? $this->signStr : '');
        }

        return $html;
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
