<?php

namespace Yugeon\HTML5Parser;

class NodeAttribute
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
     *
     * @param string $name
     * @param string|null $value
     * @param string $whitespaceBefore
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
     *
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *
     * @param string|null $value
     * @return void
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     *
     * @return string|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     *
     * @param string|null $signStr
     * @return void
     */
    public function setSignStr($signStr)
    {
        $this->signStr = $signStr;
    }

    /**
     *
     * @return string|null
     */
    public function getSignStr()
    {
        return $this->signStr;
    }

    /**
     *
     * @param string $quotesSymbol
     * @return void
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
     *
     * @return string
     */
    public function getQuotesSymbol()
    {
        return $this->quotesSymbol;
    }

    /**
     *
     * @return string
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
