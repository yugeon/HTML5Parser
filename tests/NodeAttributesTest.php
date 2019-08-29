<?php

namespace Yugeon\HTML5Parser\Tests;

use PHPUnit\Framework\TestCase;
use Yugeon\HTML5Parser\NodeAttributes;
use Yugeon\HTML5Parser\NodeAttribute;

class NodeAttributesTest extends TestCase
{

    /** @var NodeAttributes */
    private $testClass;

    function setUp()
    {
        $this->testClass = new NodeAttributes();
    }

    public function testClassCanBeInstantiated()
    {
        $this->assertTrue(is_object($this->testClass));
    }

    public function testObjectIsOfCorrectType()
    {
        $this->assertTrue(get_class($this->testClass) == 'Yugeon\HTML5Parser\NodeAttributes');
    }

    public function testCanAddAttributeToCollection()
    {
        $attr = new NodeAttribute();
        $this->assertEquals($this->testClass, $this->testClass->addAttribute($attr));
        $this->assertCount(1, $this->testClass);
    }

    public function testCanFillCollectionFromConstructor()
    {
        $attrs = [
            $a = new NodeAttribute(),
            $b = new NodeAttribute(),
            $c = new NodeAttribute(),
        ];

        $this->testClass = new NodeAttributes($attrs);
        $this->assertCount(3, $this->testClass);
    }

    public function testCanCheckIfHasAttributes()
    {
        $this->assertFalse($this->testClass->hasAttributes());

        $this->testClass->addAttribute(new NodeAttribute());
        $this->assertTrue($this->testClass->hasAttributes());
    }

    public function testCanCheckIfHasSpecificAttribute()
    {
        $name = 'class';
        $testAttr = new NodeAttribute($name);
        $this->testClass->addAttribute($testAttr);

        $this->assertTrue($this->testClass->hasAttribute($name));

    }

    public function testCanGetSpecificAttribute()
    {
        $name = 'class';
        $testAttr = new NodeAttribute($name);
        $this->testClass->addAttribute($testAttr);

        $this->assertEquals($testAttr, $this->testClass->getAttribute($name));
    }

    public function testCanRemoveSpecificAttribute()
    {
        $name = 'class';
        $testAttr = new NodeAttribute($name);
        $this->testClass->addAttribute($testAttr);
        $this->testClass->removeAttribute($name);

        $this->assertFalse($this->testClass->hasAttribute($name));
    }

    public function testAllowAttributesWithIdenticalNames()
    {
        $attrs = [
            $a = new NodeAttribute('a', '1'),
            $b = new NodeAttribute('a', '2'),
            $c = new NodeAttribute('a', '3'),
        ];
        $this->testClass->addAttributes($attrs);

        $this->assertEquals(3, $this->testClass->count());
    }

    public function testMustRetriveFirstAttributeWithSpecificName()
    {
        $attrs = [
            $a = new NodeAttribute('a', '1'),
            $b = new NodeAttribute('a', '2'),
            $c = new NodeAttribute('a', '3'),
        ];
        $this->testClass->addAttributes($attrs);

        $this->assertEquals($a, $this->testClass->getAttribute('a'));
    }

    public function testRemoveAllAttributesWithSpecificName()
    {
        $attrs = [
            $a = new NodeAttribute('a'),
            $b = new NodeAttribute('a'),
            $c = new NodeAttribute('a'),
        ];
        $this->testClass->addAttributes($attrs);

        $this->testClass->removeAttribute('a');
        $this->assertFalse($this->testClass->hasAttribute('a'));
    }

    public function testCanParseAttributesAsStr()
    {
        $attrStr = ' id="a" class="red>green" data=\'bla bla bla\' data-url = allo disabled ';
        $this->testClass->parse($attrStr);

        $this->assertCount(5, $this->testClass);
        $this->assertTrue($this->testClass->hasAttribute('id'));
        $this->assertTrue($this->testClass->hasAttribute('class'));
        $this->assertTrue($this->testClass->hasAttribute('data'));
        $this->assertTrue($this->testClass->hasAttribute('data-url'));
        $this->assertTrue($this->testClass->hasAttribute('disabled'));
        $this->assertEquals('red>green', $this->testClass->getAttribute('class')->getValue());
    }

    public function testCanRestoreAttrAsHtmlString()
    {
        $attrStr = ' id="a" disabled class="red green" color="#232390"';
        $this->testClass->parse($attrStr);
        $this->assertEquals($attrStr, $this->testClass->getHtml());
    }

}
