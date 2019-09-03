<?php

namespace Yugeon\HTML5Parser\Tests;

use PHPUnit\Framework\TestCase;
use Yugeon\HTML5Parser\TextNode;

class TextNodeTest extends TestCase
{

    /** @var TextNode */
    protected $testClass;

    function setUp()
    {
        // $doc = new \DOMDocument('1.0', 'UTF-8');
        // $this->testClass = new TextNode('test value');
        // $doc->appendChild($this->testClass);
        $this->testClass = new TextNode('text value');
    }

    public function testClassCanBeInstantiated()
    {
        $this->assertTrue(is_object($this->testClass));
    }

    public function testObjectIsOfCorrectType()
    {
        $this->assertTrue(get_class($this->testClass) == 'Yugeon\HTML5Parser\TextNode');
    }

    public function testMustBeInstanceOfDomElement()
    {
        $this->assertInstanceOf(\DOMText::class, $this->testClass);
    }

    public function testConstructorMustNotContradictParentConstructor()
    {
        $textValue = 'test';
        $this->testClass = new TextNode($textValue);
        $this->assertEquals($textValue, $this->testClass->wholeText);
    }

    public function testMustImplementNodeInterface()
    {
        $this->assertInstanceOf('Yugeon\Html5Parser\NodeInterface', $this->testClass);
    }

    public function testMustCorrectlyImplementInterface()
    {
        $this->assertTrue($this->testClass->isTextNode());
        $this->assertFalse($this->testClass->isComment());
        $this->assertFalse($this->testClass->isDoctype());
        $this->assertFalse($this->testClass->isElement());
        $this->assertFalse($this->testClass->isSelfClosingTag());
    }

    public function testCanCreateTextNode()
    {
        $textData = ' text node ';
        $this->testClass = new TextNode('');
        $this->testClass->appendData($textData);
        $this->assertEquals($textData, $this->testClass->textContent);
        $this->assertTrue($this->testClass->isTextNode());
    }

    public function testCanRestoreOriginalText()
    {
        $text = ' aksdf adlsf aldkf ';
        $this->testClass = new TextNode($text);
        $this->assertEquals($text, $this->testClass->getHtml());
    }

    public function testInnerHtmlMustBeEmpty()
    {
        $text = ' aksdf adlsf aldkf ';
        $this->testClass = new TextNode($text);
        $this->assertEquals('', $this->testClass->getInnerHtml());
    }

    public function testTextNodeTagNameMustBeText()
    {
        $text = ' aksdf adlsf aldkf ';
        $this->testClass = new TextNode($text);

        $this->assertEquals('#text', $this->testClass->nodeName);
    }

}
