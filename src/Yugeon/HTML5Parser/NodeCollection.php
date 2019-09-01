<?php

namespace Yugeon\HTML5Parser;

use Yugeon\HTML5Parser\Node;

// TODO: refactor to use ArrayObject
class NodeCollection implements NodeCollectionInterface, \Countable, \IteratorAggregate
{
    /** @var Node[] */
    protected $nodes = [];

    /** @var int */
    protected $level = 0;

    /**
     * Instantinate a collection of nodes.
     *
     * @param NodeInterface[]|string[] $nodes
     * @return void
     */
    public function __construct($nodes = [])
    {
        $this->addNodes($nodes);
    }

    /**
     * {@inheritDoc}
     */
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

    /**
     * {@inheritDoc}
     */
    public function addNode(Node $node)
    {
        $this->nodes[] = $node;
        $node->setLevel($this->level);
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->nodes);
    }

    /**
     * {@inheritDoc}
     */
    public function item($index)
    {
        return isset($this->nodes[$index]) ? $this->nodes[$index] : null;
    }

    /**
     * {@inheritDoc}
     */
    public function getItems()
    {
        return $this->nodes;
    }

    /**
     * {@inheritDoc}
     */
    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
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
    public function removeItem($index)
    {
        // TODO: check and remove links to this node.
        $deleted = array_splice($this->nodes, $index, 1);
        foreach ($deleted as $node) {
            $node->prepareForRemove();
        }

    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->nodes);
    }
}
