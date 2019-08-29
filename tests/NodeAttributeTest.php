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
            $this->testClass->setValue($value);
            $this->assertEquals($value, $this->testClass->getValue());
        }
    }

    public function testCanGetHtml()
    {
        $name = 'id';
        $value = 'some-id';
        $this->testClass = new NodeAttribute($name, $value);
        $this->assertEquals("{$name}={$value}", $this->testClass->getHtml());
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

        $this->testClass->setName('id');
        $this->testClass->setQuotesSymbol('"');

        foreach ($emptyValues as $index => $value) {
            $this->testClass->setValue($value);
            $this->assertEquals($expectedHtml[$index], $this->testClass->getHtml());
        }
    }

    public function testCanGetHtmlWithWhitespace()
    {
        $name = 'id';
        $value = 'some-id';
        $ws = '  ';
        $this->testClass = new NodeAttribute($name, $value, $ws);
        $this->assertEquals("{$ws}{$name}={$value}", $this->testClass->getHtml());
    }

    public function testMustPreservQuotes()
    {
        $name = 'id';
        $value = '" some-id "';
        $this->testClass = new NodeAttribute($name, $value);
        $this->assertEquals("{$name}={$value}", $this->testClass->getHtml());
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

}
