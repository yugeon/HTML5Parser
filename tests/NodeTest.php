<?php

namespace Yugeon\HTML5Parser\Tests;

use PHPUnit\Framework\TestCase;
use Yugeon\HTML5Parser\Node;

class NodeTest extends TestCase {

    private $testClass;

    function setUp() {
        $this->testClass = new Node();
    }

    public function testClassCanBeInstantiated() {
        $this->assertTrue(is_object($this->testClass));
    }

    public function testObjectIsOfCorrectType() {
        $this->assertTrue(get_class($this->testClass) == 'Yugeon\HTML5Parser\Node');
    }

    public function testCanPassStringToConstructor()
    {
        $htmlNode = '<div>hello';
        $this->testClass = new Node($htmlNode);
        $this->assertEquals($htmlNode, (String) $this->testClass);
    }

    public function testCanGetTagNameOfInitializedNode()
    {
        $htmlNode = '<div>hello';
        $this->testClass = new Node($htmlNode);
        $this->assertEquals('div', $this->testClass->getTagName());
    }

    public function testCanInitFromSeparateMethod()
    {
        $htmlNode = '<div>hello';
        $this->testClass->parse($htmlNode);
        $this->assertEquals('div', $this->testClass->getTagName());
        $this->assertTrue($this->testClass->isStartTag);
    }

    public function testMustClearObjectBeforeParse()
    {
        $this->assertFalse($this->testClass->isStartTag);
        $this->assertFalse($this->testClass->isEndTag);

        $htmlNode = '<div>hello';
        $this->testClass->parse($htmlNode);
        $this->assertTrue($this->testClass->isStartTag);
        $this->assertFalse($this->testClass->isEndTag);

        $htmlNode = '</div>hello';
        $this->testClass->parse($htmlNode);
        $this->assertFalse($this->testClass->isStartTag);
        $this->assertTrue($this->testClass->isEndTag);
    }

    public function testCanParseEndTag()
    {
        $htmlNode = '</div>hello';
        $this->testClass->parse($htmlNode);
        $this->assertEquals('div', $this->testClass->getTagName());
        $this->assertTrue($this->testClass->isEndTag);
    }

    public function testCanParseComments()
    {
        $htmlNode = '<!-- abrakadabra -->';
        $this->testClass->parse($htmlNode);
        $this->assertEquals('!--', $this->testClass->getTagName());
        $this->assertTrue($this->testClass->isComment());
    }

    public function testCanParseDoctype()
    {
        $htmlNode = '<!DOCTYPE html>';
        $this->testClass->parse($htmlNode);
        $this->assertEquals('!DOCTYPE', $this->testClass->getTagName());
        $this->assertTrue($this->testClass->isDoctype());
    }

    // TODO: add tests for custom tags that can also be self-closing.

    public function testCanParseTagsWithWhitespaces()
    {
        $htmlNode = '</   div  >hello';
        $this->testClass->parse($htmlNode);
        $this->assertEquals('div', $this->testClass->getTagName());
        $this->assertTrue($this->testClass->isEndTag);
    }

    public function testCanParseSelfClosingTags()
    {
        $htmlNode = '<br />';
        $this->testClass->parse($htmlNode);
        $this->assertEquals('br', $this->testClass->getTagName());
        $this->assertTrue($this->testClass->isStartTag);
        $this->assertTrue($this->testClass->isSelfClosingTag());
    }

    public function testCommentsMustBeSelfClosingTag()
    {
        $htmlNode = '<!--any comment-->';
        $this->testClass->parse($htmlNode);
        $this->assertEquals('!--', $this->testClass->getTagName());
        $this->assertTrue($this->testClass->isStartTag);
        $this->assertFalse($this->testClass->isEndTag);
        $this->assertTrue($this->testClass->isSelfClosingTag());
    }

    public function testCanRestoreOriginalStartTag()
    {
        $html = '<div>Hello world';
        $this->testClass->parse($html);
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testCanRestoreOriginalEndTag()
    {
        $html = '</div>Hello world';
        $this->testClass->parse($html);
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testCanRestoreOriginalSelfClosingTag()
    {
        $html = '<br/> Hello world';
        $this->testClass->parse($html);
        $this->assertEquals($html, $this->testClass->getHtml());

        $html = '<BR> Hello world';
        $this->testClass->parse($html);
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testCanRestoreOriginalDoctype()
    {
        $html = '<!Doctype html>';
        $this->testClass->parse($html);
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testCanRestoreOriginalComment()
    {
        $html = '<!-- any beny -->allo';
        $this->testClass->parse($html);
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testCanRestoreOriginalTagWithWhitespaces()
    {
        $html = "< \n  div >allo";
        $this->testClass->parse($html);
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testCanRestoreOriginalHtmlWithWhitespacesInEnd(Type $var = null)
    {
        $html = '<div>Hello
        ';
        $this->testClass->parse($html);
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testCanSetGetNestingLevel()
    {
        $level = 2;
        $this->testClass->setLevel($level);
        $this->assertEquals($level, $this->testClass->getLevel());
    }

    public function testCanSetGetParentNode()
    {
        $nodeA = new Node();
        $this->testClass->setParent($nodeA);
        $this->assertEquals($nodeA, $this->testClass->getParent());
    }

    public function testCanAddGetChildNode()
    {
        $childNode = new Node();
        $this->testClass->addNode($childNode);
        $this->assertContains($childNode, $this->testClass->getChilds()->getItems());
    }

    public function testCanAddGetChildNodes()
    {
        $childNodes = [
            $a = new Node('<br>World'),
            $b = new Node('</div>')
        ];
        $this->testClass->addNodes($childNodes);
        $this->assertEquals($childNodes, $this->testClass->getChilds()->getItems());
    }

    public function testCanRestoreHtmlConsideringChildNodes()
    {
        $this->testClass->parse('<div>Hello');
        $childNodes = [
            $a = new Node('<br>World'),
            $b = new Node('</div>')
        ];
        $this->testClass->addNodes($childNodes);
        $this->assertEquals('<div>Hello<br>World</div>', $this->testClass->getHtml());
    }

    public function testMustSetCorrectParentNodeAfterAddNode()
    {
        $childNode = new Node();
        $this->testClass->addNode($childNode);
        $this->assertEquals($this->testClass, $childNode->getParent());
    }

    // public function testCanAddGetChildNodes()
    // {
    //     $childNodes = [
    //         $a = new Node(),
    //         $b = new Node(),
    //         $c = new Node(),
    //     ];
    //     $this->testClass->;
    //     $this->assertEquals($nodeA, $this->testClass->getParent());
    // }

}