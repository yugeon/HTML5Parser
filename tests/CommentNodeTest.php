<?php

namespace Yugeon\HTML5Parser\Tests;

use PHPUnit\Framework\TestCase;
use Yugeon\HTML5Parser\CommentNode;

class CommentNodeTest extends TestCase
{

    /** @var CommentNode */
    private $testClass;

    function setUp()
    {
        $this->testClass = new CommentNode('');
    }

    public function testClassCanBeInstantiated()
    {
        $this->assertTrue(is_object($this->testClass));
    }

    public function testObjectIsOfCorrectType()
    {
        $this->assertTrue(get_class($this->testClass) == 'Yugeon\HTML5Parser\CommentNode');
    }

    public function testMustBeInstanceOfDomElement()
    {
        $this->assertInstanceOf(\DOMComment::class, $this->testClass);
    }

    public function testConstructorMustNotContradictParentConstructor()
    {
        $comment = ' this is comment ';
        $this->testClass = new CommentNode($comment);
        $this->assertEquals($comment, $this->testClass->nodeValue);
    }

    public function testMustImplementNodeInterface()
    {
        $this->assertInstanceOf('Yugeon\Html5Parser\NodeInterface', $this->testClass);
    }

    public function testMustCorrectlyImplementInterface()
    {
        $this->assertTrue($this->testClass->isComment());
        $this->assertFalse($this->testClass->isElement());
        $this->assertFalse($this->testClass->isDoctype());
        $this->assertFalse($this->testClass->isTextNode());
        $this->assertTrue($this->testClass->isSelfClosingTag());
    }

    public function testCanCreateCommentNode()
    {
        $textData = ' text node ';
        $this->testClass = new CommentNode('');
        $this->testClass->appendData($textData);
        $this->assertEquals($textData, $this->testClass->textContent);
        $this->assertTrue($this->testClass->isComment());
    }

    public function testInnerHtmlMustBeEmpty()
    {
        $text = ' aksdf adlsf aldkf ';
        $this->testClass = new CommentNode($text);
        $this->assertEquals('', $this->testClass->getInnerHtml());
    }

    public function testCanRestoreOriginalComment()
    {
        $html = '<!-- any beny -->';
        $this->testClass->appendData(' any beny ');
        $this->assertEquals($html, $this->testClass->getHtml());
    }
}
