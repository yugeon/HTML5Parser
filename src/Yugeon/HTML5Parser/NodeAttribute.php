<?php

namespace Yugeon\HTML5Parser;

class NodeAttribute implements NodeAttributeInterface
{
    /** @var string */
    protected $stringValue = '';

    /** @var string */
    protected $name = '';

    /** @var string|null */
    protected $value = null;

    /** @var string */
    protected $preservedWhitespace = '';

    /** @var string|null */
    protected $signStr = null;

    /** @var string */
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
    public function __construct($name = '', $value = null, $whitespaceBefore = '', $signStr = null, $quotesSymbol = '')
    {
        if (!empty($name)) {
            $this->setName($name);
        }

        if (!is_null($value)) {
            $this->setValue($value);
        }

        if (!empty($whitespaceBefore)) {
            $this->preservedWhitespace = $whitespaceBefore;
        }

        $this->setSignStr($signStr);

        $this->setQuotesSymbol($quotesSymbol);
    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {
        $this->name = $name;
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
    public function setValue($value)
    {
        $this->value = $value;
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
        if (!is_null($this->value)) {
            $html .= $this->name . (!is_null($this->signStr) ? $this->signStr : '=') . $this->quotesSymbol . $this->value . $this->quotesSymbol;
        } else {
            $html .= $this->name . (!is_null($this->signStr) ? $this->signStr : '');
        }

        return $html;
    }
}
