<?php

namespace Yugeon\HTML5Parser\Tests;

use PHPUnit\Framework\TestCase;
use Yugeon\HTML5Parser\Dom\NodeAttribute;

class NodeAttributeTest extends TestCase {

    /** @var NodeAttribute */
    private $testClass;

    function setUp() {
        $this->testClass = new NodeAttribute('test');
    }

    public function testClassCanBeInstantiated() {
        $this->assertTrue(is_object($this->testClass));
    }

    public function testObjectIsOfCorrectType() {
        $this->assertTrue(get_class($this->testClass) == 'Yugeon\HTML5Parser\Dom\NodeAttribute');
    }

    public function testMustImplementNodeAttributeInterface()
    {
        $this->assertInstanceOf('Yugeon\Html5Parser\Dom\NodeAttributeInterface', $this->testClass);
    }

    public function testCanGetAttributeName()
    {
        $this->testClass = new NodeAttribute($attributeName = 'class', 'red');
        $this->assertEquals($attributeName, $this->testClass->getName());
    }

    public function testCanGetAttributeValueAsString()
    {
        $attributeValue = 'test-value';
        $this->testClass->setValue($attributeValue);
        $this->assertEquals($attributeValue, $this->testClass->getValue());
    }

    public function testCanEncodeValue()
    {
        $value = 'test&value';
        $expectedValue = 'test&value';
        $this->testClass->setValue($value, true);
        $this->assertEquals($expectedValue, $this->testClass->value);
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
        $this->testClass = new NodeAttribute('test', $attributeValue);
        $this->assertEquals($attributeValue, $this->testClass->getValue());
    }

    public function testCanSelectQuoteSymbol()
    {
        $quotesSymbols = [
            '',
            '"',
            "'",
            ';'
        ];

        $expectedQuotes = [
            '',
            '"',
            "'",
            ''
        ];

        foreach ($quotesSymbols as $index => $quotesSymbol) {
            $this->testClass->setQuotesSymbol($quotesSymbol);
            $this->assertEquals($expectedQuotes[$index], $this->testClass->getQuotesSymbol());
        }
    }

    public function testCanSelectQuotesSymbolInConstructor()
    {
        $expected = '"';
        $this->testClass = new NodeAttribute('id', 'val', '', null, $expected);
        $this->assertEquals($expected, $this->testClass->getQuotesSymbol());
    }

    public function testAttributeCanBeEmpty()
    {
        $emptyValues = [
            '',
            0,
            '0',
            ' ',
            null
        ];

        foreach ($emptyValues as $value) {
            $this->testClass = new NodeAttribute('test', $value);
            $this->assertEquals($value, $this->testClass->getValue());
        }
    }

    public function testCanGetHtml()
    {
        $name = 'id';
        $value = 'some-id';
        $this->testClass = new NodeAttribute($name, $value);
        $this->assertEquals("{$name}=\"{$value}\"", $this->testClass->getHtml());
    }

    public function testCanGetHtmlWithEmptyValue()
    {
        $emptyValues = [
            '',
            0,
            '0',
            ' ',
            null
        ];

        $expectedHtml = [
            'id=""',
            'id="0"',
            'id="0"',
            'id=" "',
            'id',
        ];

        foreach ($emptyValues as $index => $value) {
            $this->testClass = new NodeAttribute('id', $value);
            $this->testClass->setQuotesSymbol('"');
            $this->assertEquals($expectedHtml[$index], $this->testClass->getHtml());
        }
    }

    public function testCanGetHtmlWithWhitespace()
    {
        $name = 'id';
        $value = 'some-id';
        $ws = '  ';
        $this->testClass = new NodeAttribute($name, $value, $ws);
        $this->assertEquals("{$ws}{$name}=\"{$value}\"", $this->testClass->getHtml());
    }

    public function testMustPreservQuotes()
    {
        $name = 'id';
        $value = "' some-id '";
        $this->testClass = new NodeAttribute($name, $value);
        $this->assertEquals("{$name}=\"{$value}\"", $this->testClass->getHtml());
    }

    public function testMustAutoQuotesValues()
    {
        $name = 'id';
        $value = " some-id ";
        $this->testClass = new NodeAttribute($name, $value);
        $this->assertEquals("{$name}=\"{$value}\"", $this->testClass->getHtml());
    }

    public function testCanReturnHtmlWithoutQuotes()
    {
        $name = 'id';
        $value = 'some-id';
        $this->testClass = new NodeAttribute($name, $value);
        $this->testClass->setQuotesSymbol('');
        $this->assertEquals("$name=$value", $this->testClass->getHtml());
    }

    public function testMustReturnValueWithoutQuotes()
    {
        $name = 'id';
        $expected = ' some-id ';
        $this->testClass = new NodeAttribute($name, $expected);
        $this->assertEquals($expected, $this->testClass->getValue());
    }

    public function testCanSetSignWithPreservedWhitespaces()
    {
        $signStr = ' = ';
        $this->testClass->setSignStr($signStr);
        $this->assertEquals($signStr, $this->testClass->getSignStr());
    }

    public function testCanUseSpecialCharsInValues()
    {
        $name = 'id';
        $values = [
            '',
            'some&id',
            'some&amp;id',
            'some>id',
            'some&gt;id',
            'some"id',
            'some&quot;id',
            "some'id",
            "some&apos;id",
            ")",
        ];

        foreach ($values as $value) {
            $this->testClass = new NodeAttribute($name, $value);
            $this->assertEquals($value, $this->testClass->value);
        }
    }

    public function testMustBeInstanceOfDomAttr()
    {
        $this->assertInstanceOf(\DOMAttr::class, $this->testClass);
    }

    public function testMustCorrectExtendsDomAtrr()
    {
        $name = 'id';
        $value = 'some-id';
        $this->testClass = new NodeAttribute($name, $value);

        $this->assertEquals($name, $this->testClass->name);
        $this->assertEquals($value, $this->testClass->value);
    }

    public function testCanEncodeSpecialChars()
    {
        $name = 'id';
        $value = '< > & \' "';
        $this->testClass = new NodeAttribute($name, $value, '', null, '"', true);
        $this->assertEquals('&lt; &gt; &amp; &apos; &quot;', $this->testClass->value);
    }

    public function testNotDecodeTwiceSpecialChars()
    {
        $name = 'id';
        $value = '&lt; &gt; &amp; &apos; &quot;';
        $this->testClass = new NodeAttribute($name, $value, '', null, '"', true);
        $this->assertEquals('&lt; &gt; &amp; &apos; &quot;', $this->testClass->value);
    }

    public function testDefaultNotEncodeSpecialChars()
    {
        $name = 'id';
        $value = '< > & \' "';
        $this->testClass = new NodeAttribute($name, $value);
        $this->assertEquals($value, $this->testClass->value);
    }

    public function testGetHtmlMustPreserveSpecialChars()
    {
        $name = 'id';
        $value = '< > & \' "';
        $this->testClass = new NodeAttribute($name, $value, '', null, '"', true);
        $this->assertEquals('id="&lt; &gt; &amp; &apos; &quot;"', $this->testClass->getHtml());
    }

    public function testCanSetAttributesWithNS()
    {
        $name = 'xmlns:fb';
        $value = 'http://www.facebook.com/2008/fbml';
        $this->testClass = new NodeAttribute($name, $value, '', null, '"', true);
        $this->assertEquals("{$name}=\"{$value}\"", $this->testClass->getHtml());
    }

}
