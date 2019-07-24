<?php

namespace Yugeon\HTML5Parser;

use Yugeon\HTML5Parser\Node;

class NodeCollection implements \Countable
{
    /** @var Node[] */
    protected $nodes = [];

    /** @var int */
    protected $level = 0;

    public function __construct($nodes = [])
    {
        $this->addNodes($nodes);
    }

    public function addNodes($nodes = [])
    {
        foreach ($nodes as $node) {
            if (!($node instanceof Node) && is_string($node)) {
                $node = new Node($node);
            }

            if ($node instanceof Node) {
                $this->addNode($node);
            }
        }
    }

    public function count()
    {
        return count($this->nodes);
    }

    public function item($index)
    {
        return isset($this->nodes[$index]) ? $this->nodes[$index] : null;
    }

    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function addNode(Node $node)
    {
        $this->nodes[] = $node;
        $node->setLevel($this->level);

        return true;
    }
}
