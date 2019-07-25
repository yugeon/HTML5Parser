<?php

namespace Yugeon\HTML5Parser;

class Node
{
    /** @var string */
    protected $stringValue = '';

    /** @var string */
    protected $tagName = '';

    /** @var Node */
    protected $parentNode = null;

    /** @var NodeCollection */
    protected $childs = null;

    /** @var int */
    public $level = 0;

    /** @var bool */
    public $isStartTag = false;

    /** @var bool */
    public $isEndTag = false;

    protected $whitespacesBeforeTag = '';
    protected $attributesStr = '';
    protected $textValue = '';

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

    public function __construct($stringValue = '')
    {
        $this->clear();
        $this->parse($stringValue);
    }

    public function parse($stringValue)
    {
        $this->clear();
        $this->stringValue = $stringValue;
        $this->parseStringTag($stringValue);
    }

    protected function clear()
    {
        $this->isStartTag = false;
        $this->isEndTag = false;
        $this->whitespacesBeforeTag = '';
        $this->attributesStr = '';
        $this->textValue = '';
        $this->parentNode = null;
        $this->childs = new NodeCollection();
    }

    protected function parseStringTag($stringValue)
    {
        if (empty($stringValue)) {
            return;
        }

        if (false !== preg_match('#<(/?)(\s*)(!--|[^>\s]+)([^>]*)>(.*)#is', $stringValue, $matches)) {
            if (!empty($matches[1])) {
                $this->isEndTag = true;
            } else {
                $this->isStartTag = true;
            }
            $this->whitespacesBeforeTag = $matches[2];
            $this->tagName = $matches[3];

            // TODO: parse attributes
            $this->attributesStr = $matches[4];
            $this->textValue = $matches[5];
        }
    }

    public function isDoctype()
    {
        return 1 === preg_match('#!doctype#i', $this->tagName);
    }

    public function isComment()
    {
        return $this->tagName === '!--' ? true : false;
    }

    public function getTagName()
    {
        return $this->tagName;
    }

    public function isSelfClosingTag()
    {
        return in_array(strtolower($this->tagName), $this->selfClosingTags);
    }

    public function getHtml()
    {
        $html = $this->_getSelfHtml();
        foreach ($this->getChilds() as $node) {
            $html .= $node->getHtml();
        }

        return $html;
    }

    protected function _getSelfHtml()
    {
        return
            "<" . ($this->isEndTag ? '/' : '') .
            $this->whitespacesBeforeTag .
            $this->tagName . '' .
            $this->attributesStr .
            '>' .
            $this->textValue;
    }

    public function setParent($node)
    {
        if ($node instanceof Node) {
            $this->parentNode = $node;
        }
    }

    public function getParent()
    {
        return $this->parentNode;
    }

    public function addNode($node)
    {
        $this->childs->addNode($node);
        $node->setParent($this);
    }

    public function addNodes($nodes)
    {
        foreach ($nodes as $node) {
            if ($node instanceof Node) {
                $this->addNode($node);
            }
        }
    }

    public function getChilds()
    {
        return $this->childs;
    }

    public function setLevel($level)
    {
        $this->level = $level;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function __toString()
    {
        return $this->getHtml();
    }
}
