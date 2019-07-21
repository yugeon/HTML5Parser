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

    public function testCanSetGetNestingLevel()
    {
        $level = 2;
        $this->testClass->setLevel($level);
        $this->assertEquals($level, $this->testClass->getLevel());
    }

}