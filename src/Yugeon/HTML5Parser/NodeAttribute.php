<?php

namespace Yugeon\HTML5Parser;

class NodeAttribute
{
    /** @var string */
    protected $stringValue = '';

    /** @var string */
    protected $name = '';

    /** @var string */
    protected $value;

    /** @var string */
    protected $preservedWhitespace = '';

    public function __construct($name = '', $value = '', $whitespace = '')
    {
        if (!empty($name)) {
            $this->setName($name);
        }

        if (!empty($value)) {
            $this->setValue($value);
        }

        if (!empty($whitespace)) {
            $this->preservedWhitespace = $whitespace;
        }
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getHtml()
    {
        $html = $this->preservedWhitespace;
        if ($this->value) {
            $html .= $this->name . '="' . $this->value . '"';
        } else {
            $html .= $this->name;
        }

        return $html;
    }
}
