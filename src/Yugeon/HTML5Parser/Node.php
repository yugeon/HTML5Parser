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

    /** @var Node */
    protected $endNode = null;

    /** @var NodeCollection */
    protected $childs = null;

    /** @var int */
    public $level = 0;

    /** @var bool */
    public $isStartTag = false;

    /** @var bool */
    public $isEndTag = false;

    /** @var bool */
    public $isSelfClosingTag = false;

    protected $attributesStr = '';

    /** @var NodeAttributes */
    protected $attributes = null;
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

    public function __construct($stringValue = '', $isComment = false)
    {
        $this->parse($stringValue, $isComment);
    }

    public function parse($stringValue, $isComment = false)
    {
        $this->stringValue = $stringValue;
        if ($isComment) {
            $this->parseCommentTag($stringValue);
        } else {
            $this->parseStringTag($stringValue);
        }
    }

    protected function clear()
    {
        $this->tagName = '';
        $this->isStartTag = false;
        $this->isEndTag = false;
        $this->isSelfClosingTag = false;
        $this->attributesStr = '';
        $this->textValue = '';
        $this->parentNode = null;
        $this->childs = new NodeCollection();
        $this->attributes = new NodeAttributes();
        $this->level = 0;
    }

    protected function parseStringTag($stringValue)
    {
        $this->clear();

        if (empty($stringValue)) {
            return;
        }

        if (false !== preg_match(
            '#<(?<end1>/)?(?<tag>[^!\s>/]+|!doctype)(?<attr>\s+(?:[^\'"/>]+|".*?"|\'.*?\')*)?(?<end2>\s*/)?>(?<html>.*)#is',
            $stringValue,
            $matches
        )) {
            if (!empty($matches['end1'])) {
                $this->isEndTag = true;
            } else {
                $this->isStartTag = true;
            }

            if (!empty($matches['end2'])) {
                $this->isSelfClosingTag = true;
            }

            if (isset($matches['tag'])) {
                $this->tagName = $matches['tag'];
            } else {
                // try parse as comment
                // TODO: mb warning
                $this->parseCommentTag($stringValue);
                return;
                // throw new \Exception('Seems you forgot to set the flag that this is a comment');
            }

            if (isset($matches['attr'])) {
                $this->attributesStr = $matches['attr'];
                if (!$this->isComment()) {
                    $this->attributes->parse($this->attributesStr);
                }
            }

            if (isset($matches['html'])) {
                $this->textValue = $matches['html'];
            }
        }
    }

    protected function parseCommentTag($stringValue)
    {
        $this->clear();

        if (empty($stringValue)) {
            return;
        }

        if (false !== preg_match('#<(?<tag>!--)(?<comment>.*?)-->(?<html>.*)#is', $stringValue, $matches)) {
            if (isset($matches['tag'])) {
                $this->tagName = $matches['tag'];
            } else {
                // TODO: not comment
                //throw new \Exception("Seems [{$stringValue}] not comment");
                return;
            }

            $this->isStartTag = true;
            $this->isEndTag = false;
            $this->attributesStr = isset($matches['comment']) ? $matches['comment'] : '';

            if (isset($matches['html'])) {
                $this->textValue = $matches['html'];
            }
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
        if ($this->isSelfClosingTag) {
            return true;
        }

        return in_array(strtolower($this->tagName), $this->selfClosingTags);
    }

    public function getHtml()
    {
        $html = $this->_getSelfHtml();
        foreach ($this->getChilds() as $node) {
            $html .= $node->getHtml();
        }

        $html .= ($this->endNode ? $this->endNode->getHtml() : '');

        return $html;
    }

    protected function _getSelfHtml()
    {
        return
            "<" . ($this->isEndTag ? '/' : '') .
            $this->tagName . '' .
            $this->attributes->getHtml() .
            ($this->isSelfClosingTag ? '/' : '') .
            ($this->isComment() ? $this->attributesStr . '--' : '') .
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

    /**
     * Add child node
     *
     * @param  Node $node
     * @return void
     */
    public function addNode($node)
    {
        $this->childs->addNode($node);
        $node->setParent($this);
        $node->setLevel($this->getLevel() + 1);
    }

    /**
     * Add child nodes
     *
     * @param Node[] $nodes
     * @return void
     */
    public function addNodes($nodes)
    {
        foreach ($nodes as $node) {
            if ($node instanceof Node) {
                $this->addNode($node);
            }
        }
    }

    /**
     * Add end node
     *
     * @param Node $node
     * @return void
     */
    public function addEndNode($node)
    {
        $this->endNode = $node;
        $this->endNode->setParent($this);
        $this->endNode->setLevel($this->getLevel());
    }

    /**
     *
     * @return Node|null
     */
    public function getEndNode()
    {
        return $this->endNode;
    }

    /**
     *
     * @return NodeCollection
     */
    public function getChilds()
    {
        return $this->childs;
    }

    // TODO: methods work around child nodes

    public function setLevel($level)
    {
        $this->level = $level;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getAttribute($name)
    {
        return $this->attributes->getAttribute($name);
    }

    public function hasAttributes()
    {
        return $this->attributes->hasAttributes();
    }

    public function hasAttribute($name)
    {
        return $this->attributes->hasAttribute($name);
    }

    public function removeAttribute($name)
    {
        $this->attributes->removeAttribute($name);
    }

    public function __toString()
    {
        return $this->getHtml();
    }
}
