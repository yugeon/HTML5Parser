<?php

namespace Yugeon\HTML5Parser;

class Node implements NodeInterface
{
    /** @var string */
    protected $stringValue = '';

    /** @var string */
    protected $tagName = '';

    /** @var NodeInterface */
    protected $parentNode = null;

    /** @var NodeInterface */
    protected $endNode = null;

    /** @var NodeCollectionInterface */
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

    /** @var NodeAttributesInterface */
    protected $attributes = null;

    /** @var bool */
    public $isTextNode = false;

    /** @var string */
    protected $textData = '';

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

    /**
     * Instatinate Node.
     *
     * @param string $stringValue
     * @param boolean $isComment
     */
    public function __construct($stringValue = '', $isComment = false)
    {
        $this->parse($stringValue, $isComment);
    }

    /**
     * Parse string tag and fill this node properties.
     *
     * @param string $stringValue
     * @param boolean $isComment
     * @return void
     */
    public function parse($stringValue, $isComment = false)
    {
        $this->stringValue = $stringValue;
        if ($isComment) {
            $this->parseCommentTag($stringValue);
        } else {
            $this->parseStringTag($stringValue);
        }
    }

    /**
     * Clear node for reuse.
     *
     * @return void
     */
    protected function clear()
    {
        $this->tagName = '';
        $this->isStartTag = false;
        $this->isEndTag = false;
        $this->isSelfClosingTag = false;
        $this->attributesStr = '';
        $this->parentNode = null;
        $this->childs = new NodeCollection();
        $this->attributes = new NodeAttributes();
        $this->level = 0;
        $this->isTextNode = false;
        $this->textData = '';
    }

    /**
     * Parse string tag.
     *
     * @param string $stringValue
     * @return void
     */
    protected function parseStringTag($stringValue)
    {
        $this->clear();

        if (empty($stringValue)) {
            return;
        }

        if (false !== preg_match(
            '#<(?<end1>/)?(?<tag>[^!\s>/]+|!doctype)(?<attr>\s+(?:[^\'"/>]+|".*?"|\'.*?\')*)?(?<end2>\s*/)?>#is',
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
        }
    }

    /**
     * Parse string comment tag
     *
     * @param string $stringValue
     * @return void
     */
    protected function parseCommentTag($stringValue)
    {
        $this->clear();

        if (empty($stringValue)) {
            return;
        }

        if (false !== preg_match('#<(?<tag>!--)(?<comment>.*?)-->#is', $stringValue, $matches)) {
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
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isDoctype()
    {
        return 1 === preg_match('#!doctype#i', $this->tagName);
    }

    /**
     * {@inheritDoc}
     */
    public function isComment()
    {
        return $this->tagName === '!--' ? true : false;
    }

    /**
     * {@inheritDoc}
     */
    public function getTagName()
    {
        return $this->tagName;
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
    public function getHtml()
    {
        $html = $this->_getSelfHtml();
        $html .= $this->getInnerHtml();

        $html .= ($this->endNode ? $this->endNode->getHtml() : '');

        return $html;
    }

    /**
     * {@inheritDoc}
     */
    protected function _getSelfHtml()
    {
        $selfHtml = '';

        if ($this->isTextNode()) {
            $selfHtml = $this->getTextData();
        } else {
            $selfHtml =
                "<" . ($this->isEndTag ? '/' : '') .
                $this->tagName . '' .
                $this->attributes->getHtml() .
                ($this->isSelfClosingTag ? '/' : '') .
                ($this->isComment() ? $this->attributesStr . '--' : '') .
                '>';
        }

        return $selfHtml;

            // . $this->textValue;
    }

    /**
     * {@inheritDoc}
     */
    public function getInnerHtml()
    {
        $html = '';

        foreach ($this->getChilds() as $node) {
            $html .= $node->getHtml();
        }

        return $html;
    }

    /**
     * {@inheritDoc}
     */
    public function setParent($node)
    {
        if ($node instanceof Node) {
            $this->parentNode = $node;
            $this->level = ($node->getLevel() + 1);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return $this->parentNode;
    }

    /**
     * {@inheritDoc}
     */
    public function addNode($node)
    {
        $this->childs->addNode($node);
        $node->setParent($this);
        $node->setLevel($this->getLevel() + 1);
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function addEndNode($node)
    {
        $this->endNode = $node;
        $this->endNode->setParent($this);
        $this->endNode->setLevel($this->getLevel());
    }

    /**
     * {@inheritDoc}
     */
    public function getEndNode()
    {
        return $this->endNode;
    }

    /**
     * {@inheritDoc}
     */
    public function getChilds()
    {
        return $this->childs;
    }

    // TODO: methods work around child nodes

    /**
     * {@inheritDoc}
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * {@inheritDoc}
     */
    public function getLevel()
    {
        return $this->level;
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
        return $this->attributes->getAttribute($name);
    }

    /**
     * {@inheritDoc}
     */
    public function hasAttributes()
    {
        return $this->attributes->hasAttributes();
    }

    /**
     * {@inheritDoc}
     */
    public function hasAttribute($name)
    {
        return $this->attributes->hasAttribute($name);
    }

    /**
     * {@inheritDoc}
     */
    public function removeAttribute($name)
    {
        $this->attributes->removeAttribute($name);
    }

    /**
     * {@inheritDoc}
     */
    public function clearAttributes()
    {
        $this->attributes->clearAttributes();
    }

    public function __toString()
    {
        return $this->getHtml();
    }

    public function prepareForRemove()
    {
        $this->parentNode = null;

        if (!is_null($this->endNode)) {
            $this->endNode->prepareForRemove();
        }
        $this->endNode = null;

        foreach ($this->childs as $childNode) {
            $childNode->prepareForRemove();
        }
    }

    public function isTextNode()
    {
        return $this->isTextNode;
    }

    public function addTextData($textData)
    {
        $this->textData = $textData;
        $this->isTextNode = true;
        $this->tagName = '#text';
    }

    public function getTextData()
    {
        if ($this->isTextNode()) {
            return $this->textData;
        }

        return '';
    }
}
