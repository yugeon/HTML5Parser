<?php

namespace Yugeon\HTML5Parser\Tests;

use PHPUnit\Framework\TestCase;
use Yugeon\HTML5Parser\NodeCollection;
use Yugeon\HTML5Parser\Node;

class NodeCollectionTest extends TestCase {

    private $testClass;

    function setUp() {
        $this->testClass = new NodeCollection();
    }

    public function testClassCanBeInstantiated() {
        $this->assertTrue(is_object($this->testClass));
    }

    public function testObjectIsOfCorrectType() {
        $this->assertTrue(get_class($this->testClass) == 'Yugeon\HTML5Parser\NodeCollection');
    }

    public function testCanAddNodeToCollection()
    {
        $node = new Node();
        $this->assertTrue($this->testClass->addNode($node));
        $this->assertCount(1, $this->testClass);
    }

    public function testCanFillCollectionFromConstructor()
    {
        $nodes = [
            $a = new Node(),
            $b = new Node(),
            $c = new Node(),
        ];

        $this->testClass = new NodeCollection($nodes);
        $this->assertCount(3, $this->testClass);
        $this->assertEquals($b, $this->testClass->item(1));
    }

    public function testCanConvertStringsToNodesInConstructor()
    {
        $nodes = [
            $a = '<div>Hello',
            $b = new Node(),
            $c = new Node(),
        ];

        $this->testClass = new NodeCollection($nodes);
        $this->assertEquals('div', $this->testClass->item(0)->getTagName());
    }

    public function testCanIgnoreNotStringAndNotNodeItemsInConstructor()
    {
        $nodes = [
            $a = '<div>Hello',
            $b = 1,
            $c = new NodeCollection(),
        ];

        $this->testClass = new NodeCollection($nodes);
        $this->assertCount(1, $this->testClass);
    }

    public function testCanGetNodeItemByIndex()
    {
        $node = new Node();
        $this->testClass->addNode($node);
        $this->assertEquals($node, $this->testClass->item(0));
    }

    public function testCanAddNodesToCollection()
    {
        $nodes = [
            $a = new Node(),
            $b = new Node(),
            $c = new Node(),
        ];
        $this->testClass->addNodes($nodes);
        $this->assertEquals($b, $this->testClass->item(0));
        $this->assertCount(3, $this->testClass);
    }

    public function testCanTraverseByCollectionAsByArray()
    {
        $nodes = [
            $a = new Node(),
            $b = new Node(),
            $c = new Node(),
        ];
        $this->testClass->addNodes($nodes);
        $i = 0;
        foreach ($this->testClass as $node) {
            $this->assertEquals($nodes[$i++], $node);
        }
        $this->assertEquals($i, 3);
    }

    public function testCanSetGetLevelNodesInCollection()
    {
        $level = 1;
        $this->testClass->setLevel($level);
        $this->assertEquals($level, $this->testClass->getLevel());
    }

    public function testAllNodesInCollectionMustBeInSameLevel()
    {
        $level = 1;
        $nodes = [
            $a = new Node(),
            $b = new Node(),
            $c = new Node(),
        ];

        $this->testClass->setLevel($level);
        $this->testClass->addNodes($nodes);
        $this->assertEquals($level, $this->testClass->item(0)->getLevel());
        $this->assertEquals($level, $this->testClass->item(1)->getLevel());
        $this->assertEquals($level, $this->testClass->item(2)->getLevel());
    }

    public function testCanGetNodeItems()
    {
        $nodes = [
            $a = new Node(),
            $b = new Node(),
            $c = new Node(),
        ];
        $this->testClass->addNodes($nodes);
        $this->assertEquals($nodes, $this->testClass->getItems());
    }

}