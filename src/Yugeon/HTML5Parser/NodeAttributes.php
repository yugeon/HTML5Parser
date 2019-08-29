<?php

namespace Yugeon\HTML5Parser;

class NodeAttributes implements \Countable, \IteratorAggregate
{
    /** @var NodeAttribute[] */
    protected $attributes = [];

    /** @var \Traversable */
    protected $arrayIterator = null;

    /** @var string */
    protected $beginPreservedWhitespace = '';

    /** @var string */
    protected $endPreservedWhitespace = '';

    public function __construct($attributes = [])
    {
        $this->addAttributes($attributes);
    }

    public function addAttributes($attributes = [])
    {
        foreach ($attributes as $attribute) {
            if (!($attribute instanceof NodeAttribute) && is_string($attribute)) {
                $this->parse($attribute);
            }

            $this->addAttribute($attribute);
        }
    }

    public function parse($attrStr)
    {
        if (empty($attrStr)) {
            return;
        }

        if (false !== preg_match_all(
            '#(?<ws>\s+)?(?<name>[^\s=\'"]+)?(?:(?<sign>\s*=\s*)(?:"(?<value1>.*?)"|\'(?<value2>.*?)\'|(?<value3>[^\'">\s]+))?)?#is',
            $attrStr,
            $matches,
            PREG_SET_ORDER
        )) {
            if (isset($matches)) {
                $this->buildAttributes($matches);
            }
        }
    }

    protected function buildAttributes($attrs)
    {
        foreach ($attrs as $attr) {
            // process first or last whitespaces
            if (!isset($attr['name'])) {
                if (!empty($attr['ws'])) {
                    if ($this->hasAttributes()) {
                        $this->endPreservedWhitespace = $attr['ws'];
                    } else {
                        $this->beginPreservedWhitespace = $attr['ws'];
                    }
                }
                continue;
            }

            $name = $attr['name'];

            $quotesSymbol = '';
            $value = null;
            if (isset($attr['value3'])) {
                $value = $attr['value3'];
                $quotesSymbol = '';
            } else if (isset($attr['value2'])) {
                $value = $attr['value2'];
                $quotesSymbol = '\'';
            } else if (isset($attr['value1'])) {
                $value = $attr['value1'];
                $quotesSymbol = '"';
            }

            $signStr = !empty($attr['sign']) ? $attr['sign'] : null;
            $whitespaceBefore = !empty($attr['ws']) ? $attr['ws'] : '';

            $this->addAttribute(new NodeAttribute($name, $value, $whitespaceBefore, $signStr, $quotesSymbol));
        }
    }

    /**
     * Add attribute to collection
     *
     * @param NodeAttribute $attr
     * @return void
     */
    public function addAttribute($attr)
    {
        if ($attr instanceof NodeAttribute) {
            $this->attributes[] = $attr;
        }

        return $this;
    }

    public function hasAttributes()
    {
        return $this->count() > 0;
    }

    public function hasAttribute($name)
    {
        if ($this->getAttribute($name)) {
            return true;
        }

        return false;
    }

    public function getAttribute($name)
    {
        foreach ($this->attributes as $attribute) {
            if ($name === $attribute->getName()) {
                return $attribute;
            }
        }

        return false;
    }

    public function removeAttribute($name)
    {
        foreach ($this->attributes as $key => $attribute) {
            if ($name === $attribute->getName()) {
                unset($this->attributes[$key]);
            }
        }

        if (!$this->hasAttributes()) {
            $this->clearAttributes();
        }
    }

    public function clearAttributes()
    {
        $this->attributes = [];
        $this->beginPreservedWhitespace = '';
        $this->endPreservedWhitespace = '';
    }

    public function getHtml()
    {
        $html = $this->beginPreservedWhitespace;
        foreach ($this->attributes as $attribute) {
            $html .= $attribute->getHtml();
        }

        return $html . $this->endPreservedWhitespace;
    }

    public function count()
    {
        return count($this->attributes);
    }

    public function getIterator()
    {
        if (!$this->arrayIterator) {
            $this->arrayIterator = new \ArrayIterator($this->attributes);
        }

        return $this->arrayIterator;
    }
}
