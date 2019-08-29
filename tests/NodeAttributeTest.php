<?php

namespace Yugeon\HTML5Parser\Tests;

use PHPUnit\Framework\TestCase;
use Yugeon\HTML5Parser\NodeAttribute;

class NodeAttributeTest extends TestCase {

    /** @var NodeAttribute */
    private $testClass;

    function setUp() {
        $this->testClass = new NodeAttribute();
    }

    public function testClassCanBeInstantiated() {
        $this->assertTrue(is_object($this->testClass));
    }

    public function testObjectIsOfCorrectType() {
        $this->assertTrue(get_class($this->testClass) == 'Yugeon\HTML5Parser\NodeAttribute');
    }

    public function testCanSetGetAttributeName()
    {
        $attributeName = 'class';
        $this->testClass->setName($attributeName);
        $this->assertEquals($attributeName, $this->testClass->getName());
    }

    public function testCanSetGetAttributeValueAsString()
    {
        $attributeValue = 'test-value';
        $this->testClass->setValue($attributeValue);
        $this->assertEquals($attributeValue, $this->testClass->getValue());
    }

    public function testCanInitializeFromConstructor()
    {
        $name = 'id';
        $value = 'some-id';
        $this->testClass = new NodeAttribute($name, $value);
        $this->assertEquals($name, $this->testClass->getName());
        $this->assertEquals($value, $this->testClass->getValue());
    }

    public function testCanPreservWhitespacesInAttributeValue()
    {
        $attributeValue = '  test  value ';
        $this->testClass->setValue($attributeValue);
        $this->assertEquals($attributeValue, $this->testClass->getValue());
    }

    public function testAttributeCanBeEmpty()
    {
        $attributeValue = '';
        $this->testClass->setValue($attributeValue);
        $this->assertEquals($attributeValue, $this->testClass->getValue());
    }

    public function testCanGetHtml()
    {
        $name = 'id';
        $value = 'some-id';
        $this->testClass = new NodeAttribute($name, $value);
        $this->assertEquals("{$name}=\"{$value}\"", $this->testClass->getHtml());
    }

    public function testCanGetHtmlWithWhitespace()
    {
        $name = 'id';
        $value = 'some-id';
        $ws = '  ';
        $this->testClass = new NodeAttribute($name, $value, $ws);
        $this->assertEquals("{$ws}{$name}=\"{$value}\"", $this->testClass->getHtml());
    }

}
