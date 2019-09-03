<?php

namespace Yugeon\HTML5Parser\Tests;

use PHPUnit\Framework\TestCase;
use Yugeon\HTML5Parser\ElementNode;
use Yugeon\HTML5Parser\NodeAttribute;
use Yugeon\HTML5Parser\NodeAttributeInterface;
use Yugeon\HTML5Parser\TextNode;

class ElementNodeTest extends TestCase
{

    /** @var ElementNode */
    private $testClass;

    function setUp()
    {
        $doc = new \DOMDocument('1.0', 'UTF-8');
        $this->testClass = new ElementNode('div');
        $doc->appendChild($this->testClass);
    }

    public function testClassCanBeInstantiated()
    {
        $this->assertTrue(is_object($this->testClass));
    }

    public function testObjectIsOfCorrectType()
    {
        $this->assertTrue(get_class($this->testClass) == 'Yugeon\HTML5Parser\ElementNode');
    }

    public function testMustBeInstanceOfDomElement()
    {
        $this->assertInstanceOf(\DOMElement::class, $this->testClass);
    }

    public function testConstructorMustNotContradictParentConstructor()
    {
        $tagName = 'div';
        $this->testClass = new ElementNode($tagName);
        $this->assertEquals($tagName, $this->testClass->tagName);
    }

    public function testMustCorrectExtendsDomElement()
    {
        $tagName = 'div';
        $this->testClass = new ElementNode($tagName);
        $this->assertEquals($tagName, $this->testClass->tagName);
    }

    public function testMustImplementNodeInterface()
    {
        $this->assertInstanceOf('Yugeon\Html5Parser\NodeInterface', $this->testClass);
    }

    public function testMustImplementElementNodeInterface()
    {
        $this->assertInstanceOf('Yugeon\Html5Parser\ElementNodeInterface', $this->testClass);
    }

    public function testMustCorrectlyImplementInterface()
    {
        $this->assertTrue($this->testClass->isElement());
        $this->assertFalse($this->testClass->isComment());
        $this->assertFalse($this->testClass->isDoctype());
        $this->assertFalse($this->testClass->isTextNode());
        $this->assertFalse($this->testClass->isSelfClosingTag());
    }

    public function testCanPassStringToConstructor()
    {
        $this->testClass = new ElementNode('div');
        $this->assertEquals('<div>', (string) $this->testClass);
    }

    public function testCanGetTagNameOfInitializedNode()
    {
        $tagName = 'div';
        $this->testClass = new ElementNode($tagName);
        $this->assertEquals($tagName, $this->testClass->tagName);
    }

    public function testCanAddGetChildNode()
    {
        $childNode = new ElementNode('span');
        $this->testClass->appendChild($childNode);
        $this->assertContains($childNode, $this->testClass->childNodes);
    }

    public function testCanRestoreOriginalStartTag()
    {
        $this->assertEquals('<div>', $this->testClass->getHtml());
    }

    public function testCanPreserveSelfClosingSlash()
    {
        $this->testClass = new ElementNode('br');
        $this->testClass->setSelfClosing(true);
        $this->assertEquals('<br/>', $this->testClass->getHtml());
    }

    public function testCanParseSelfClosingTagsWithWhitespaces()
    {
        $this->testClass = new ElementNode('br');
        $this->testClass->setSelfClosing(true);
        $this->testClass->setWhitespaces(' ');

        $htmlNode = '<br />';
        $this->assertEquals('br', $this->testClass->tagName);
        $this->assertEquals($htmlNode, $this->testClass->getHtml());
    }

    public function testCanGetInnerHtml()
    {
        $childNode = new ElementNode('h1');
        $this->testClass->appendChild($childNode);

        $this->assertEquals('<h1>', $this->testClass->getInnerHtml());
    }

    public function testCanSetCloseTag()
    {
        $endTag = '</div>';
        $this->testClass->addEndTag($endTag);

        $this->assertEquals($endTag, $this->testClass->getEndTag());
    }

    public function testCanRestoreHtmlConsideringEndNode()
    {
        $endTag = '</div>';
        $this->testClass->addEndTag($endTag);

        $this->assertEquals('<div>' . $endTag, $this->testClass->getHtml());
    }

    public function testCanRestoreHtmlConsideringChildNodes()
    {
        $this->testClass->addEndTag('</div>');

        $a = new ElementNode('br');
        $b = new ElementNode('div');
        $b->addEndTag('</div>');

        $this->testClass->appendChild($a);
        $this->testClass->appendChild($b);

        $this->assertEquals('<div><br><div></div></div>', $this->testClass->getHtml());
    }

    public function testCanAddGetAttributeNode()
    {
        $attr = new NodeAttribute('id', 'z', ' ', ' = ', "'");
        $this->testClass->setAttributeNode($attr);
        $this->assertEquals($attr, $this->testClass->getAttributeNode('id'));
        $this->assertEquals('id', $this->testClass->getAttributeNode('id')->getName());
        $this->assertInstanceOf(NodeAttributeInterface::class, $this->testClass->getAttributeNode('id'));
    }

    public function testCanSetAttributeOnNode()
    {
        $attrValue = 'value';
        $this->testClass->setAttribute('id', $attrValue);
        $this->assertEquals($attrValue, $this->testClass->getAttribute('id'));
    }

    public function testAttributeNodeMustBeInstanceOfNodeAttribute()
    {
        $this->testClass->setAttribute('id', 'z');
        $this->assertInstanceOf(NodeAttributeInterface::class, $this->testClass->getAttributeNode('id'));
    }

    public function testCanCheckIfNodeHasAttributes()
    {
        $attr = new NodeAttribute('id', 'z');
        $this->testClass->setAttributeNode($attr);
        $this->assertTrue($this->testClass->hasAttributes());
    }

    public function testCanCheckIfNodeHasSpecificAttribute()
    {
        $attr = new NodeAttribute('class', 'z');
        $this->testClass->setAttributeNode($attr);
        $this->assertTrue($this->testClass->hasAttribute('class'));
    }

    public function testCanGetNodeSpecificAttribute()
    {
        $attr = new NodeAttribute('id', 'z');
        $this->testClass->setAttributeNode($attr);
        $this->assertEquals('z', $this->testClass->getAttributeNode('id')->value);
    }

    public function testCanRemoveNodeSpecificAttribute()
    {
        $attr = new NodeAttribute('id', 'z');
        $this->testClass->setAttributeNode($attr);
        $this->testClass->removeAttribute('id');
        $this->assertFalse($this->testClass->hasAttribute('id'));
    }

    public function testMustAutoCloseTagIfChildNodesExist()
    {
        $childEl = new TextNode('hello');
        $this->testClass->appendChild($childEl);
        $this->assertEquals('<div>hello</div>', $this->testClass->getHtml());
    }

    public function testMustPreserveRefToChildNodes()
    {
        $childEl = new TextNode('hello');
        $this->testClass->appendChild($childEl);
        $this->testClass->addEndTag('</div>');
        $this->assertEquals('<div>hello</div>', $this->testClass->getHtml());
    }
}
