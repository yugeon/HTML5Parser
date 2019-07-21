<?php

namespace Yugeon\HTML5Parser;

class Node
{
    /** @var string */
    protected $stringValue = '';

    /** @var string */
    protected $tagName = '';

    /** @var int */
    protected $level;

    public function __construct($stringValue = '')
    {
        $this->stringValue = $stringValue;
        $this->parseStringTag($stringValue);
    }

    protected function parseStringTag($stringValue)
    {
        if (empty($stringValue)) {
            return;
        }

        if (false !== preg_match('#<([^>]+\b|!--)([^>]*)>(.*)#i', $stringValue, $matches)) {
            $this->tagName = $matches[1];
            // $this->attributes = $matches[1];
            // $this->textValue = $matches[1];
        }
    }

    public function getTagName()
    {
        return $this->tagName;
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
