<?php

namespace Yugeon\HTML5Parser\Tests;

use PHPUnit\Framework\TestCase;
use stdClass;
use Yugeon\HTML5Parser\NodeAttributes;
use Yugeon\HTML5Parser\NodeAttribute;
use Yugeon\HTML5Parser\NodeAttributeInterface;

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

    public function testMustImplementNodeAttributesInterface()
    {
        $this->assertInstanceOf('Yugeon\Html5Parser\NodeAttributesInterface', $this->testClass);
    }

    public function testCanAddAttributeToCollection()
    {
        $attr = new NodeAttribute();
        $this->assertEquals($this->testClass, $this->testClass->addAttribute($attr));
        $this->assertCount(1, $this->testClass);
    }

    public function testCanAddAttributeAsString()
    {
        $attrStr = 'id = "value"';
        $this->testClass->addAttribute($attrStr);
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

    public function testCanAddAttributesAsArrayOfDifferentTypes()
    {
        $attrs = [
            $a = new NodeAttribute(),
            'b = "value"',
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

    public function testCanClearAttributes()
    {
        $attrs = [
            $a = new NodeAttribute('a', '1', ' ', '"'),
            $b = new NodeAttribute('b'),
            $c = new NodeAttribute('c'),
        ];
        $this->testClass->addAttributes($attrs);

        $this->testClass->clearAttributes();
        $this->assertFalse($this->testClass->hasAttribute('a'));
        $this->assertCount(0, $this->testClass);
    }

    public function testMustClearAllWhitespacesAfterClearAttributes()
    {
        $attrStr = ' id="a" class="red>green" data=\'bla bla bla\' data-url = allo disabled ';
        $this->testClass->parse($attrStr);
        $this->testClass->clearAttributes();
        $this->assertEmpty($this->testClass->getHtml());
    }

    public function testMustClearAllWhitespacesAfterRemoveLastAttribute()
    {
        $attrStr = ' id="a" ';
        $this->testClass->parse($attrStr);
        $this->testClass->removeAttribute('id');
        $this->assertEmpty($this->testClass->getHtml());
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
        $attrsStr = [
            ' id="a" disabled class="red green" color="#232390"',
            ' charset=UTF-8',
            ' property=\'og:title\' content="Women\'s Shorts & Skorts in Denim, Khaki, & Jersey | Ralph Lauren" ',
            '<img onerror="this.remove()" id="altImgPlp" src=\'https://www.rlmedia.io/is/image/PoloGSI/s7-1333158_lifestyle?$rl_392_pdp$\' ',
            '<input type=\'hidden\' class=\'mailchimp_extra_field\' id="utm_location" value=\'Header_login\'
                name=\'location\'',
            "img class=\"minithumbimg\" width='110' height='120' ",
            ' href="https://www.dollskill.com/shop-brands/" title=',
        ];

        foreach ($attrsStr as $attrStr) {
            $this->testClass->clearAttributes();
            $this->testClass->parse($attrStr);
            $this->assertEquals($attrStr, $this->testClass->getHtml());
        }
    }

    public function testCanRestoreEmptyAttributesValue()
    {
        $attrs = [
            'value=0',
            ' id="bogo_item_count" type="hidden" value=0 ',
            ' class="country tree-item has-children" role="treeitem" tabindex="0"',
            ' type="hidden" class="user-data-country" value="" ',
            " type='text' id='login-popup_honeypot' value='' name='honeypot'",
        ];

        foreach ($attrs as $attr) {
            $this->testClass->clearAttributes();
            $this->testClass->parse($attr);
            $this->assertEquals($attr, $this->testClass->getHtml());
        }
    }

    public function testMustPreserveAttributeEqualsSignAndWhitespacesAround()
    {
        $attrs = [
            'id="value"',
            'id = "value"',
            'id ="value"',
            'id= "value"',
            'id=',
            'id =',
            'id= ',
            'id = ',
            'id'
        ];

        foreach ($attrs as $attr) {
            $this->testClass->clearAttributes();
            $this->testClass->parse($attr);
            $this->assertEquals($attr, $this->testClass->getHtml());
        }
    }

    public function testCanInjectNodeAttributeDependencyClass()
    {
        $newNodeAttributeClass = $this->getMockClass('Yugeon\\HTML5Parser\\NodeAttributeInterface');
        $this->testClass->injectNodeAttributeClass($newNodeAttributeClass);
        $this->assertEquals($newNodeAttributeClass, $this->testClass->getNodeAttributeClass());
    }

    public function testNotAllowInjectClassThatNotImplementRelevantInterface()
    {
        $newNodeAttributeClass = $this->getMockClass(stdClass::class);
        $oldNodeAttributeClass = $this->testClass->getNodeAttributeClass();

        $this->testClass->injectNodeAttributeClass($newNodeAttributeClass);
        $this->assertEquals($oldNodeAttributeClass, $this->testClass->getNodeAttributeClass());
    }

    public function testInjectedClassMustBeCalled()
    {
        $html = 'id="value"';
        $newNodeAttributeClass = $this->getMockClass('Yugeon\\HTML5Parser\\NodeAttributeInterface');
        $this->testClass->injectNodeAttributeClass($newNodeAttributeClass);
        $this->testClass->parse($html);
        $this->assertInstanceOf($newNodeAttributeClass, $this->testClass->getAttributes()[0]);
    }
}
