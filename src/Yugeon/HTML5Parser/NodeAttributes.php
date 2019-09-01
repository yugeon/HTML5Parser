<?php

namespace Yugeon\HTML5Parser;

// TODO: refactor to use ArrayObject
class NodeAttributes implements NodeAttributesInterface, \Countable, \IteratorAggregate
{
    /** @var NodeAttributeInterface[] */
    protected $attributes = [];

    /** @var string */
    protected $beginPreservedWhitespace = '';

    /** @var string */
    protected $endPreservedWhitespace = '';

    /** @var string */
    protected $nodeAttributeClassName = __NAMESPACE__ . '\\NodeAttribute';

    /**
     * Initialize collection
     *
     * @param NodeAttributeInterface[]|string[] $attributes
     */
    public function __construct($attributes = [])
    {
        $this->addAttributes($attributes);
    }

    /**
     * Parses a string containing html attributes.
     * @example 'id="abc" class="red" href="//site.domain/" disabled'
     *
     * @param string $attrStr
     * @return void
     */
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

    /**
     * Build attributes collection
     *
     * @param array $attrs
     * @return void
     */
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

            $this->addAttribute(new $this->nodeAttributeClassName($name, $value, $whitespaceBefore, $signStr, $quotesSymbol));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function addAttribute($attribute)
    {
        if (!($attribute instanceof NodeAttributeInterface) && is_string($attribute)) {
            $this->parse($attribute);
        } else if ($attribute instanceof NodeAttributeInterface) {
            $this->attributes[] = $attribute;
        } else {
            return false;
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addAttributes($attributes = [])
    {
        foreach ($attributes as $attribute) {
            $this->addAttribute($attribute);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function hasAttributes()
    {
        return $this->count() > 0;
    }

    /**
     * {@inheritDoc}
     */
    public function hasAttribute($name)
    {
        if ($this->getAttribute($name)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * {@inheritDoc}
     */
    public function getAttribute($name)
    {
        foreach ($this->attributes as $attribute) {
            if ($name === $attribute->getName()) {
                return $attribute;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
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

    /**
     * {@inheritDoc}
     */
    public function clearAttributes()
    {
        $this->attributes = [];
        $this->beginPreservedWhitespace = '';
        $this->endPreservedWhitespace = '';
    }

    /**
     * {@inheritDoc}
     */
    public function getHtml()
    {
        $html = $this->beginPreservedWhitespace;
        foreach ($this->attributes as $attribute) {
            $html .= $attribute->getHtml();
        }

        return $html . $this->endPreservedWhitespace;
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->attributes);
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->attributes);
    }

    /**
     * Inject name of class, that implements NodeAttributeInterface
     *
     * @param string $nodeAttributeClassName Must implement NodeAttributeInterface
     * @return void
     */
    public function injectNodeAttributeClass($nodeAttributeClassName)
    {
        if (in_array(NodeAttributeInterface::class, class_implements($nodeAttributeClassName))) {
            $this->nodeAttributeClassName = $nodeAttributeClassName;
        }
    }

    /**
     * Getting class name that implement NodeAttributeInterface
     *
     * @return string
     */
    public function getNodeAttributeClass()
    {
        return $this->nodeAttributeClassName;
    }
}
