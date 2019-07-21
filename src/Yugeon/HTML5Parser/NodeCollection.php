<?php

namespace Yugeon\HTML5Parser;

class NodeCollection implements \Countable
{
    protected $nodes = [];

    public function __construct($nodes = [])
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

    public function addNode(Node $node)
    {
        $this->nodes[] = $node;
        return true;
    }
}
