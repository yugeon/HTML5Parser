<?php

namespace Yugeon\HTML5Parser;

interface NodeCollectionInterface
{
    /**
     * Add nodes to the end of the collection.
     *
     * @param NodeInterface[]|string[] $nodes
     * @return void
     */
    public function addNodes($nodes = []);

    /**
     * Add node to the end of the collection.
     *
     * @param NodeInterface $node
     * @return void
     */
    public function addNode(Node $node);

    /**
     * Count all nodes in the collection.
     *
     * @return int
     */
    public function count();

    /**
     * Getting node item from collection by index
     *
     * @param int $index
     * @return NodeInterface|null
     */
    public function item($index);

    /**
     * Retrieves all nodes from the collection.
     *
     * @return NodeInterface[]
     */
    public function getItems();

    /**
     * Set level of nodes in the collection.
     *
     * @param int $level
     * @return void
     */
    public function setLevel($level);

    /**
     * Getting level of nodes in the collection.
     *
     * @return int
     */
    public function getLevel();

    /**
     * Remove node item from the collection by index.
     *
     * @param int $index
     * @return void
     */
    public function removeItem($index);
}
