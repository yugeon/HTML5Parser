<?php

namespace Yugeon\HTML5Parser\Tests;

use PHPUnit\Framework\TestCase;
use Yugeon\HTML5Parser\Node;

class NodeTest extends TestCase
{

    /** @var Node */
    private $testClass;

    function setUp()
    {
        $this->testClass = new Node();
    }

    public function testClassCanBeInstantiated()
    {
        $this->assertTrue(is_object($this->testClass));
    }

    public function testObjectIsOfCorrectType()
    {
        $this->assertTrue(get_class($this->testClass) == 'Yugeon\HTML5Parser\Node');
    }

    public function testMustImplementNodeAttributesInterface()
    {
        $this->assertInstanceOf('Yugeon\Html5Parser\NodeInterface', $this->testClass);
    }

    public function testCanPassStringToConstructor()
    {
        $htmlNode = '<div>';
        $this->testClass = new Node($htmlNode);
        $this->assertEquals($htmlNode, (string) $this->testClass);
    }

    public function testCanGetTagNameOfInitializedNode()
    {
        $htmlNode = '<div>';
        $this->testClass = new Node($htmlNode);
        $this->assertEquals('div', $this->testClass->getTagName());
    }

    public function testCanInitFromSeparateMethod()
    {
        $htmlNode = '<div>';
        $this->testClass->parse($htmlNode);
        $this->assertEquals('div', $this->testClass->getTagName());
        $this->assertTrue($this->testClass->isStartTag);
    }

    public function testMustClearObjectBeforeParse()
    {
        $this->assertFalse($this->testClass->isStartTag);
        $this->assertFalse($this->testClass->isEndTag);

        $htmlNode = '<div>';
        $this->testClass->parse($htmlNode);
        $this->assertTrue($this->testClass->isStartTag);
        $this->assertFalse($this->testClass->isEndTag);

        $htmlNode = '</div>';
        $this->testClass->parse($htmlNode);
        $this->assertFalse($this->testClass->isStartTag);
        $this->assertTrue($this->testClass->isEndTag);
    }

    public function testCanParseEndTag()
    {
        $htmlNode = '</div>';
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
        $htmlNode = '<div  id="abc">';
        $this->testClass->parse($htmlNode);
        $this->assertEquals('div', $this->testClass->getTagName());
        $this->assertTrue($this->testClass->isStartTag);
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

    public function testCommentsMustBeSelfClosingTag()
    {
        $htmlNode = '<!--any comment-->';
        $this->testClass->parse($htmlNode);
        $this->assertEquals('!--', $this->testClass->getTagName());
        $this->assertTrue($this->testClass->isComment());
        $this->assertTrue($this->testClass->isStartTag);
        $this->assertFalse($this->testClass->isEndTag);
        $this->assertTrue($this->testClass->isSelfClosingTag());
    }

    public function testCanRestoreOriginalStartTag()
    {
        $html = '<div>';
        $this->testClass->parse($html);
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testCanRestoreOriginalEndTag()
    {
        $html = '</div>';
        $this->testClass->parse($html);
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testCanRestoreOriginalSelfClosingTag()
    {
        $html = '<br/>';
        $this->testClass->parse($html);
        $this->assertEquals($html, $this->testClass->getHtml());

        $html = '<BR>';
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
        $html = '<!-- any beny -->';
        $this->testClass->parse($html, true);
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testCanRestoreOriginalTagWithWhitespaces()
    {
        $html = '<div  id="abc" >';
        $this->testClass->parse($html);
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testCanGetInnerHtml()
    {
        $childNodeHtml = '<h1>';
        $childText = 'hello';
        $childNodeEndHtml = '</h1>';

        $childNode = new Node($childNodeHtml);

        $childTextNode = new Node();
        $childTextNode->addTextData($childText);
        $childNode->addNode($childTextNode);

        $childNode->addEndNode(new Node($childNodeEndHtml));
        $this->testClass->addNode($childNode);

        $this->assertEquals($childNodeHtml . $childText . $childNodeEndHtml, $this->testClass->getInnerHtml());
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

    public function testCanCloseNodeByOtherNode()
    {
        $startNode = new Node('<span>');
        $endNode = new Node('</span>');
        $startNode->addEndNode($endNode);

        $this->assertEquals($endNode, $startNode->getEndNode());
    }

    public function testCanRestoreHtmlConsideringEndNode()
    {
        $startTag = '<span>';
        $childText = 'hello';
        $endTag = '</span>';
        $startNode = new Node($startTag);
        $childTextNode = new Node();
        $childTextNode->addTextData($childText);
        $startNode->addNode($childTextNode);
        $endNode = new Node($endTag);
        $startNode->addEndNode($endNode);

        $this->assertEquals($startTag . $childText . $endTag, $startNode->getHtml());
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
            $a = new Node('<br>'),
            $b = new Node('<div>')
        ];
        $this->testClass->addNodes($childNodes);
        $this->assertEquals($childNodes, $this->testClass->getChilds()->getItems());
    }

    public function testChildNodesMustBeUpperLevelThanNode()
    {
        $level = 3;
        $this->testClass->setLevel($level);
        $childNodes = [
            $a = new Node('<br>'),
            $b = new Node('<div>')
        ];
        $this->testClass->addNodes($childNodes);
        $this->assertEquals($level + 1, $this->testClass->getChilds()->item(1)->getLevel());
    }

    public function testCanRestoreHtmlConsideringChildNodes()
    {
        $this->testClass->parse('<div>');
        $childNodes = [
            $a = new Node('<br>'),
            $b = new Node('<div>')
        ];
        $this->testClass->addNodes($childNodes);
        $this->assertEquals('<div><br><div>', $this->testClass->getHtml());
    }

    public function testMustSetCorrectParentNodeAfterAddNode()
    {
        $childNode = new Node();
        $this->testClass->addNode($childNode);
        $this->assertEquals($this->testClass, $childNode->getParent());
    }



    public function testCanParseTagsWithAnyAttributes()
    {
        $html = '<div class="red>green">';
        $this->testClass->parse($html);

        $this->assertEquals('div', $this->testClass->getTagName());
        $this->assertCount(0, $this->testClass->getChilds());
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testCanParseAttributes()
    {
        $this->testClass->parse('<div id="2" class="red" custom-attr=\'hello world\'>');
        $this->assertCount(3, $this->testClass->getAttributes());
    }

    public function testCanCheckIfNodeHasAttributes()
    {
        $this->testClass->parse('<div id="2" class="red" custom-attr=\'hello world\'>');
        $this->assertTrue($this->testClass->hasAttributes());
    }

    public function testCanCheckIfNodeHasSpecificAttribute()
    {
        $this->testClass->parse('<div id="2" class="red" custom-attr=\'hello world\'>');
        $this->assertTrue($this->testClass->hasAttribute('class'));
        $this->assertFalse($this->testClass->hasAttribute('not-attr'));
    }

    public function testCanGetNodeSpecificAttribute()
    {
        $this->testClass->parse('<div id="2" class="red" custom-attr=\'hello world\'>');
        $this->assertEquals('2', $this->testClass->getAttribute('id')->getValue());
    }

    public function testCanRemoveNodeSpecificAttribute()
    {
        $this->testClass->parse('<div id="2" class="red" custom-attr=\'hello world\'>');
        $this->testClass->removeAttribute('id');
        $this->assertFalse($this->testClass->hasAttribute('id'));
    }

    public function testCanCreateTextNode()
    {
        $textData = ' text node ';
        $this->testClass = new Node();
        $this->testClass->addTextData($textData);
        $this->assertEquals($textData, $this->testClass->getTextData());
        $this->assertTrue($this->testClass->isTextNode());
    }

    public function testTextNodeMustCorrectlyReturnHtml()
    {
        $textData = ' text node ';
        $this->testClass = new Node();
        $this->testClass->addTextData($textData);

        $this->assertEquals($textData, $this->testClass->getHtml());
    }
}
