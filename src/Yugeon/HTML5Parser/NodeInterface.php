<?php

namespace Yugeon\HTML5Parser;

/**
 * Represetn HTML Node
 */
interface NodeInterface
{

    /**
     * True if current node is doctype.
     *
     * @return boolean
     */
    public function isDoctype();

    /**
     * True if current node is comment.
     *
     * @return boolean
     */
    public function isComment();

    /**
     * Getting node tag name.
     *
     * @return string
     */
    public function getTagName();

    /**
     * True if current node is self closing tag.
     *
     * @return boolean
     */
    public function isSelfClosingTag();

    /**
     * Getting outer html.
     *
     * @return string
     */
    public function getHtml();

    /**
     * Getting inner html.
     *
     * @return string
     */
    public function getInnerHtml();

    /**
     * Set parent node
     *
     * @param NodeInterface $node
     * @return void
     */
    public function setParent($node);

    /**
     * Getting parent node.
     *
     * @return NodeInterface
     */
    public function getParent();

    /**
     * Add child node.
     *
     * @param  NodeInterface $node
     * @return void
     */
    public function addNode($node);

    /**
     * Add child nodes.
     *
     * @param NodeInterface[] $nodes
     * @return void
     */
    public function addNodes($nodes);

    /**
     * Add end node for this node.
     *
     * @param NodeInterface $node
     * @return void
     */
    public function addEndNode($node);

    /**
     * Getting end node for this node.
     *
     * @return NodeInterface|null
     */
    public function getEndNode();

    /**
     * Getting instance for child nodes.
     *
     * @return NodeCollectionInterface
     */
    public function getChilds();

    // TODO: methods work around child nodes

    /**
     * Set level of this node.
     *
     * @param int $level
     * @return void
     */
    public function setLevel($level);

    /**
     * Getting current level of this node.
     *
     * @return int
     */
    public function getLevel();

    /**
     * Getting an instance that represents node attributes.
     *
     * @return NodeAttributesInterface
     */
    public function getAttributes();

    /**
     * Getting an instance that represent sepcific attribute.
     *
     * @param string $name
     * @return NodeAttributeInterface
     */
    public function getAttribute($name);

    /**
     * Checks if current node has an attributes.
     *
     * @return boolean
     */
    public function hasAttributes();

    /**
     * Checks if current node has a specific attribute.
     *
     * @param string $name
     * @return boolean
     */
    public function hasAttribute($name);

    /**
     * Remove a specific attribute from the current node.
     *
     * @param string $name
     * @return void
     */
    public function removeAttribute($name);

    /**
     * Clear all attributes from the current node.
     *
     * @return void
     */
    public function clearAttributes();
}
