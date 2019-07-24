<?php

namespace Yugeon\HTML5Parser;

use phpDocumentor\Reflection\Types\Boolean;

class Node
{
    /** @var string */
    protected $stringValue = '';

    /** @var string */
    protected $tagName = '';

    /** @var int */
    public $level = 0;

    /** @var Boolean */
    public $isStartTag = false;

    /** @var Boolean */
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
    ];

    public function __construct($stringValue = '')
    {
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
    }

    protected function parseStringTag($stringValue)
    {
        if (empty($stringValue)) {
            return;
        }

        if (false !== preg_match('#<(/?)(\s*)([^>\s]+|!--)([^>]*)>(.*)#i', $stringValue, $matches)) {
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
        return
            "<" . ($this->isEndTag ? '/' : '') .
            $this->whitespacesBeforeTag .
            $this->tagName . '' .
            $this->attributesStr .
            '>' .
            $this->textValue;
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
        return $this->stringValue;
    }
}
