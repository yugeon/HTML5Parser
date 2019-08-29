<?php

namespace Yugeon\HTML5Parser\Tests;

use PHPUnit\Framework\TestCase;
use Yugeon\HTML5Parser\Node;

class NodeTest extends TestCase {

    /** @var Node */
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
        $this->testClass->parse($htmlNode, true);
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
        $htmlNode = '</div  id="abc">hello';
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

    public function testCanPreserveSelfClosingSlash()
    {
        $htmlNode = '<br />';
        $this->testClass->parse($htmlNode);
        $this->assertEquals($htmlNode, $this->testClass->getHtml());
    }

    // /**
    //  * @expectedException \Exception
    //  */
    // public function testMustThrowExceptionIfNodeIsCommentButCommentFlagNotSet()
    // {
    //     $commentNode = '<!-- any comment -->';
    //     $this->testClass->parse($commentNode);
    // }

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
        $this->testClass->parse($html, true);
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testCanRestoreOriginalTagWithWhitespaces()
    {
        $html = '<div  id="abc" >allo';
        $this->testClass->parse($html);
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testCanRestoreOriginalHtmlWithWhitespacesInHtml()
    {
        $html = "<div>Hello \t\n";
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

    public function testChildNodesMustBeUpperLevelThanNode()
    {
        $level = 3;
        $this->testClass->setLevel($level);
        $childNodes = [
            $a = new Node('<br>World'),
            $b = new Node('</div>')
        ];
        $this->testClass->addNodes($childNodes);
        $this->assertEquals($level + 1, $this->testClass->getChilds()->item(1)->getLevel());
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



    public function testCanParseTagsWithAnyAttributes()
    {
        $html = '<div class="red>green">Hello';
        $this->testClass->parse($html);
        $this->assertEquals('div', $this->testClass->getTagName());
        $this->assertEmpty($this->testClass->getChilds());
        $this->assertEquals($html, $this->testClass->getHtml());

    }

    public function testCanParseAttributes()
    {
        $this->testClass->parse('<div id="2" class="red" custom-attr=\'hello world\'>!--Hello world !doctype');
        $this->assertCount(3, $this->testClass->getAttributes());
    }

    public function testCanCheckIfNodeHasAttributes()
    {
        $this->testClass->parse('<div id="2" class="red" custom-attr=\'hello world\'>!--Hello world !doctype');
        $this->assertTrue($this->testClass->hasAttributes());
    }

    public function testCanCheckIfNodeHasSpecificAttribute()
    {
        $this->testClass->parse('<div id="2" class="red" custom-attr=\'hello world\'>!--Hello world !doctype');
        $this->assertTrue($this->testClass->hasAttribute('class'));
        $this->assertFalse($this->testClass->hasAttribute('not-attr'));
    }

    public function testCanGetNodeSpecificAttribute()
    {
        $this->testClass->parse('<div id="2" class="red" custom-attr=\'hello world\'>!--Hello world !doctype');
        $this->assertEquals('2', $this->testClass->getAttribute('id')->getValue());
    }

    public function testCanRemoveNodeSpecificAttribute()
    {
        $this->testClass->parse('<div id="2" class="red" custom-attr=\'hello world\'>!--Hello world !doctype');
        $this->testClass->removeAttribute('id');
        $this->assertFalse($this->testClass->hasAttribute('id'));
    }

}