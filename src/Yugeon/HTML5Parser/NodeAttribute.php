<?php

namespace Yugeon\HTML5Parser;

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
     * @param string $quotesSymbol Quotes symbol around attribute value. Default - no quotes.
     */
    public function __construct($name, $value = null, $whitespaceBefore = '', $signStr = null, $quotesSymbol = '')
    {

        $this->origValue = $value;

        $this->preservedWhitespace = $whitespaceBefore;

        $this->setSignStr($signStr);

        $this->setQuotesSymbol($quotesSymbol);

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
}
