<?php

namespace Yugeon\HTML5Parser;

/**
 * Contains a collection of attributes and can manipulation this collection.
 */
interface NodeAttributesInterface
{

    /**
     * Add attribute to existing collection.
     *
     * @param NodeAttributeInterface|string $attribute
     * @return void
     */
    public function addAttribute($attribute);

    /**
     * Add attributes to existing collection.
     *
     * @param  NodeAttributeInterface[]|string[] $attributes
     * @return void
     */
    public function addAttributes($attributes = []);

    /**
     * Checks whether the collection has attributes.
     *
     * @return boolean
     */
    public function hasAttributes();

    /**
     * Checks whether the collection has specific attribute.
     *
     * @param string $name Attribute name
     * @return boolean
     */
    public function hasAttribute($name);

    /**
     * Retrieves all attributes from the collection.
     *
     * @return NodeAttributeInterface[]
     */
    public function getAttributes();

    /**
     * Retrieves an instance of an attribute from the collection by the attribute name.
     *
     * @param string $name Attribute name
     * @return NodeAttributeInterface|false
     */
    public function getAttribute($name);

    /**
     * Removing an instance of an attribute from a collection by attribute name.
     *
     * @param string $name Attribute name
     * @return void
     */
    public function removeAttribute($name);

    /**
     * Clear the collection of attributes.
     *
     * @return void
     */
    public function clearAttributes();

    /**
     * Create an html string with all attributes from this collection.
     *
     * @return string
     */
    public function getHtml();

    /**
     * Count all attributes in collection.
     *
     * @return int
     */
    public function count();
}
