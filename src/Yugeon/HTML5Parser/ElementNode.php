<?php

namespace Yugeon\HTML5Parser;

class ElementNode extends \DOMElement implements NodeInterface, ElementNodeInterface
{
    /** @var string */
    protected $endTag = '';

    /** @var bool */
    public $isEndTag = false;

    /** @var bool */
    public $isSelfClosingTag = false;

    /** @var string */
    protected $whitespaceAfter = '';

    /**
     * Collection for fix refs to attributes object.
     *
     * @var NodeAttributes[]
     */
    protected $_attrs = [];

    // http://xahlee.info/js/html5_non-closing_tag.html
    protected $selfClosingTags = [
        'area',
        'base',
        'br',
        'col',
        'embed',
        'hr',
        'img',
        'input',
        'link',
        'meta',
        'param',
        'source',
        'track',
        'wbr',
        'command',
        'keygen',
        'menuitem',
        '!--',
        '!doctype',
    ];


    public function __construct($name, $value = '', $namespaceUri = '')
    {
        // disable auto create child text node
        parent::__construct($name, '', $namespaceUri);
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
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isTextNode()
    {
        return false;
    }

    public function setSelfClosing($isSelfClosing = true)
    {
        $this->isSelfClosingTag = $isSelfClosing;
    }

    /**
     * {@inheritDoc}
     */
    public function isSelfClosingTag()
    {
        if ($this->isSelfClosingTag) {
            return true;
        }

        return in_array(strtolower($this->tagName), $this->selfClosingTags);
    }

    /**
     * {@inheritDoc}
     */
    public function setWhitespaceAfter($ws)
    {
        $this->whitespaceAfter = $ws;
    }

    /**
     * {@inheritDoc}
     */
    public function getWhitespaceAfter()
    {
        return $this->whitespaceAfter;
    }

    /**
     * {@inheritDoc}
     */
    public function getHtml()
    {
        $html = $this->_getSelfHtml();

        $innerHtml = $this->getInnerHtml();
        $html .= $innerHtml;

        if (!empty($innerHtml) && empty($this->endTag)) {
                $html .= "</{$this->tagName}>";
        }

        $html .= ($this->endTag ? $this->endTag : '');

        return $html;
    }

    /**
     * Generate html of the current tag.
     *
     * @return string
     */
    protected function _getSelfHtml()
    {
        return
            "<" . ($this->isEndTag ? '/' : '')
            . $this->tagName
            . $this->_getAttributesHtml()
            . $this->getWhitespaceAfter()
            . ($this->isSelfClosingTag ? '/' : '')
            . '>';
    }

    /**
     * Generate html for the current tag attributes.
     *
     * @return string
     */
    protected function _getAttributesHtml()
    {
        $attributesHtml = '';

        foreach ($this->attributes as $attribute) {
            $attributesHtml .= $attribute->getHtml();
        }

        return $attributesHtml;
    }

    /**
     * {@inheritDoc}
     */
    public function getInnerHtml()
    {
        $html = '';

        foreach ($this->childNodes as $node) {
            $html .= $node->getHtml();
        }

        return $html;
    }

    /**
     * {@inheritDoc}
     */
    public function addEndTag($endTag)
    {
        $this->endTag = $endTag;
    }

    /**
     * {@inheritDoc}
     */
    public function getEndTag()
    {
        return $this->endTag;
    }

    /**
     * {@inheritDoc}
     */
    public function setAttributeNode($attr)
    {
        // fix ref to attribute object
        $this->_attrs[$attr->name] = $attr;
        return parent::setAttributeNode($attr);
    }

    /**
     * {@inheritDoc}
     */
    public function setAttribute($name, $value, $whitespaceBefore = '', $signStr = null, $quotesSymbol = '')
    {
        $attr = new NodeAttribute($name, $value, $whitespaceBefore, $signStr, $quotesSymbol);
        return $this->setAttributeNode($attr);
    }

    public function appendChild($node)
    {
        $this->_nodes[] = $node;
        return parent::appendChild($node);
    }

    public function __toString()
    {
        return $this->getHtml();
    }
}
