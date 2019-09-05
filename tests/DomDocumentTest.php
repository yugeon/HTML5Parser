<?php

namespace Yugeon\HTML5Parser\Tests;

use PHPUnit\Framework\TestCase;
use Yugeon\HTML5Parser\DomDocument;
use Yugeon\HTML5Parser\ElementNode;
use Yugeon\HTML5Parser\TextNode;

class DomDocumentTest extends TestCase
{

    private $testClass;

    function setUp()
    {
        $this->testClass = new DomDocument();
    }

    public function testClassCanBeInstantiated()
    {
        $this->assertTrue(is_object($this->testClass));
    }

    public function testObjectIsOfCorrectType()
    {
        $this->assertTrue(get_class($this->testClass) == 'Yugeon\HTML5Parser\DomDocument');
    }

    public function testMustBeInstanceOfDomDocument()
    {
        $this->assertInstanceOf(\DOMDocument::class, $this->testClass);
    }

    public function testConstructorMustNotContradictParentConstructor()
    {
        $version = '1.0';
        $encoding = 'UTF-8';
        $this->testClass = new DomDocument($version, $encoding);
        $this->assertEquals($version, $this->testClass->version);
        $this->assertEquals($encoding, $this->testClass->encoding);
    }

    public function testMustImplementDocumentInterface()
    {
        $this->assertInstanceOf('Yugeon\Html5Parser\DomDocumentInterface', $this->testClass);
    }

    public function testCanAddGetChildNode()
    {
        $childNode = new ElementNode('span');
        $this->testClass->appendChild($childNode);
        $this->assertContains($childNode, $this->testClass->childNodes);
    }

    public function testCanGetHtml()
    {
        $divEl = new ElementNode('div');
        $this->testClass->appendChild($divEl);
        $divEl->appendChild(new TextNode('hello'));
        $this->assertEquals('<div>hello', $this->testClass->getHtml());
    }

    public function testCanGetHtmlOfSpecificNode()
    {
        $divEl = new ElementNode('div');
        $this->testClass->appendChild($divEl);

        $spanEl = new ElementNode('span');
        $divEl->appendChild($spanEl);
        $spanEl->appendChild(new TextNode('hello'));

        $this->assertEquals('<span>hello', $this->testClass->getHtml($spanEl));
    }

    public function testCanSetPreservedDocumentWhitespace()
    {
        $whitespace = '
        ';
        $this->testClass->setPreservedDocumentWhitespace($whitespace);
        $this->assertEquals($whitespace, $this->testClass->getPreservedDocumentWhitespace());
    }

    // public function testCanRestoreOriginalStartTag()
    // {
    //     $this->assertEquals('<div>', $this->testClass->getHtml());
    // }

    // public function testCanPreserveSelfClosingSlash()
    // {
    //     $this->testClass = new ElementNode('br');
    //     $this->testClass->setSelfClosing(true);
    //     $this->assertEquals('<br/>', $this->testClass->getHtml());
    // }

    // public function testCanParseSelfClosingTagsWithWhitespaces()
    // {
    //     $this->testClass = new ElementNode('br');
    //     $this->testClass->setSelfClosing(true);
    //     $this->testClass->setWhitespaces(' ');

    //     $htmlNode = '<br />';
    //     $this->assertEquals('br', $this->testClass->tagName);
    //     $this->assertEquals($htmlNode, $this->testClass->getHtml());
    // }

    // public function testCanGetInnerHtml()
    // {
    //     $childNode = new ElementNode('h1');
    //     $this->testClass->appendChild($childNode);

    //     $this->assertEquals('<h1>', $this->testClass->getInnerHtml());
    // }

    // public function testCanSetCloseTag()
    // {
    //     $endTag = '</div>';
    //     $this->testClass->addEndTag($endTag);

    //     $this->assertEquals($endTag, $this->testClass->getEndTag());
    // }

    // public function testCanRestoreHtmlConsideringEndNode()
    // {
    //     $endTag = '</div>';
    //     $this->testClass->addEndTag($endTag);

    //     $this->assertEquals('<div>' . $endTag, $this->testClass->getHtml());
    // }

    // public function testCanRestoreHtmlConsideringChildNodes()
    // {
    //     $this->testClass->addEndTag('</div>');

    //     $a = new ElementNode('br');
    //     $b = new ElementNode('div');
    //     $b->addEndTag('</div>');

    //     $this->testClass->appendChild($a);
    //     $this->testClass->appendChild($b);

    //     $this->assertEquals('<div><br><div></div></div>', $this->testClass->getHtml());
    // }

    // public function testCanAddGetAttributeNode()
    // {
    //     $attr = new NodeAttribute('id', 'z', ' ', ' = ', "'");
    //     $this->testClass->setAttributeNode($attr);
    //     $this->assertEquals($attr, $this->testClass->getAttributeNode('id'));
    //     $this->assertEquals('id', $this->testClass->getAttributeNode('id')->getName());
    //     $this->assertInstanceOf(NodeAttributeInterface::class, $this->testClass->getAttributeNode('id'));
    // }

    // public function testCanSetAttributeOnNode()
    // {
    //     $attrValue = 'value';
    //     $this->testClass->setAttribute('id', $attrValue);
    //     $this->assertEquals($attrValue, $this->testClass->getAttribute('id'));
    // }

    // public function testAttributeNodeMustBeInstanceOfNodeAttribute()
    // {
    //     $this->testClass->setAttribute('id', 'z');
    //     $this->assertInstanceOf(NodeAttributeInterface::class, $this->testClass->getAttributeNode('id'));
    // }

    // public function testCanCheckIfNodeHasAttributes()
    // {
    //     $attr = new NodeAttribute('id', 'z');
    //     $this->testClass->setAttributeNode($attr);
    //     $this->assertTrue($this->testClass->hasAttributes());
    // }

    // public function testCanCheckIfNodeHasSpecificAttribute()
    // {
    //     $attr = new NodeAttribute('class', 'z');
    //     $this->testClass->setAttributeNode($attr);
    //     $this->assertTrue($this->testClass->hasAttribute('class'));
    // }

    // public function testCanGetNodeSpecificAttribute()
    // {
    //     $attr = new NodeAttribute('id', 'z');
    //     $this->testClass->setAttributeNode($attr);
    //     $this->assertEquals('z', $this->testClass->getAttributeNode('id')->value);
    // }

    // public function testCanRemoveNodeSpecificAttribute()
    // {
    //     $attr = new NodeAttribute('id', 'z');
    //     $this->testClass->setAttributeNode($attr);
    //     $this->testClass->removeAttribute('id');
    //     $this->assertFalse($this->testClass->hasAttribute('id'));
    // }

    // public function testResultOfParseMustBeDomDocument()
    // {
    //     $html = '<div><h1>Hello <br /> World</h1></div>';
    //     $this->assertInstanceOf(\DOMDocument::class, $this->testClass->parse($html));
    // }

    // public function testCanGetParseResultAsDomDocument()
    // {
    //     $html = '<div><h1>Hello <br /> World</h1></div>';
    //     $this->testClass->parse($html);

    //     $this->assertInstanceOf(\DOMDocument::class, $this->testClass->getDomDocument());
    // }

    // public function testCanGetParsedHtmlWithoutChanges()
    // {
    //     $html = '<div><h1>Hello <br/> World</h1></div>';
    //     $this->testClass->parse($html);
    //     $this->assertEquals($html, $this->testClass->getHtml());
    // }

    // public function testCanConsiderComments()
    // {
    //     $html = '<div><!--h1>Hello <br /> World</h1--></div>';
    //     $this->testClass->parse($html);
    //     $this->assertEquals($html, $this->testClass->getHtml());
    // }

    // public function testCanConsiderBreakLineAndWhitspaces()
    // {
    //     $html = '<div>
    //                 <p>Hello</p>
    //                 <!-- comment -->
    //                 <div>
    //                     World
    //                 </div>
    //             </div>';
    //     $this->testClass->parse($html);
    //     $this->assertEquals($html, $this->testClass->getHtml());
    // }

    // public function testCanConsiderScriptsAndTemplates()
    // {
    //     $html = '<div>
    //                 <p>Hello</p>
    //                 <!-- comment -->
    //                 <script>
    //                     var a = "<body></body>";
    //                 </script>
    //                 <template>
    //                     <div>hello</div>
    //                 </template>
    //             </div>';
    //     $this->testClass->parse($html);
    //     $this->assertEquals($html, $this->testClass->getHtml());
    // }

    // public function testCloseTagNotCreateNewNode()
    // {
    //     $html = '<div>hello</div>';
    //     $this->testClass->parse($html);
    //     $this->assertEquals(1, $this->testClass->getDomDocument()->childNodes->length);
    // }

    // public function testCommentsNotCreateChildNodes()
    // {
    //     $html = '<!--[if gt IE 8]><!--><html><!--<![endif]-->
    //              </html>';
    //     $this->testClass->parse($html);
    //     $this->assertEquals($html, $this->testClass->getHtml());
    //     $this->assertEquals(2, $this->testClass->getDomDocument()->childNodes->length);
    // }

    // public function testCanGetWorkTime()
    // {
    //     $html = '<div><h1>Hello <br /> World</h1></div>';
    //     $this->testClass->parse($html);
    //     $this->assertTrue(is_float($this->testClass->getWorkTime()));
    // }


    // public function testTextNodeMustCreateSeparateNode()
    // {
    //     $html = '<div>Hello</div>World';
    //     $this->testClass->parse($html);
    //     $this->assertEquals(2, $this->testClass->getDomDocument()->childNodes->length);
    //     $this->assertEquals($html, $this->testClass->getHtml());
    // }

    // public function testMustCorrectParseTagsWithAnyAttributes()
    // {
    //     $html = '<div class="red>green">Hello</div>';
    //     $this->testClass->parse($html);

    //     $this->assertEquals(1, $this->testClass->getDomDocument()->childNodes->length);

    //     $this->assertEquals('div', $this->testClass->getDomDocument()->childNodes->item(0)->tagName);
    //     $this->assertEquals($html, $this->testClass->getHtml());
    // }

    // public function testMustPreservWhitespacesBeforeDoctype()
    // {
    //     $html = '
    //         <!DOCTYPE html>
    //         <html></html>
    //     ';
    //     $this->testClass->parse($html);
    //     $this->assertEquals($html, $this->testClass->getHtml());
    // }

    // public function testMustClearDocumentBeforeParse()
    // {
    //     $html = '<div class="red">Hello</div>';
    //     $this->testClass->parse($html);

    //     $html = '<div class="red">Hello</div>';
    //     $this->testClass->parse($html);
    //     $this->assertEquals($html, $this->testClass->getHtml());
    // }

    // public function testCanParseAttributes()
    // {
    //     $html = '<div id="2" class="red" custom-attr=\'hello world\'>';
    //     $this->testClass->parse($html);
    //     $this->assertEquals($html, $this->testClass->getHtml());
    // }


    // public function testCanParseOneTag()
    // {
    //     $html = '<div>';
    //     $this->testClass->parse($html);
    //     $this->assertEquals($html, $this->testClass->getHtml());
    // }

    // public function testCanParseComments()
    // {
    //     $html = '<!-- abrakadabra -->';
    //     $this->testClass->parse($html, true);
    //     $this->assertEquals($html, $this->testClass->getHtml());
    // }

    // public function testCanParseDoctype()
    // {
    //     $html = '<!DOCTYPE html>';
    //     $this->testClass->parse($html);
    //     $this->assertEquals($html, $this->testClass->getHtml());
    // }

    // public function testCanParseCommentsBeforeDoctype()
    // {
    //     $html = '<!-- comment -->
    //             <!DOCTYPE html>';
    //     $this->testClass->parse($html);
    //     $this->assertEquals($html, $this->testClass->getHtml());
    // }

    // public function testCanParseTagsWithWhitespaces()
    // {
    //     $html = '<div  id="abc">';
    //     $this->testClass->parse($html);
    //     $this->assertEquals($html, $this->testClass->getHtml());
    // }

    // public function testCanParseSelfClosingTags()
    // {
    //     $html = '<br />';
    //     $this->testClass->parse($html);
    //     $this->assertEquals($html, $this->testClass->getHtml());
    // }

    // public function testCanRestoreOriginalTagWithWhitespaces()
    // {
    //     $html = '<div  id="abc" >';
    //     $this->testClass->parse($html);
    //     $this->assertEquals($html, $this->testClass->getHtml());
    // }

    // public function testIgnoreNotHtmlTags()
    // {
    //     $html = '<?xml version="1.0" encoding="UTF-8"?\>';
    //     $this->testClass->parse($html);
    //     $this->assertEquals('', $this->testClass->getHtml());
    // }

    // public function testCanParseCustomTags()
    // {
    //     $html = '<custom-tag>some text</custom-tag>
    //             <yet-another-custom-tag />
    //             <and-some-one>
    //             ';
    //     $this->testClass->parse($html);
    //     $this->assertEquals($html, $this->testClass->getHtml());
    // }
}
