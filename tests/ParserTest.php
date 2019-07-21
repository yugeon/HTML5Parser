<?php

namespace Yugeon\HTML5Parser\Tests;

use PHPUnit\Framework\TestCase;
use Yugeon\HTML5Parser\Parser;

class ParserTest extends TestCase {

    private $testClass;

    function setUp() {
        $this->testClass = new Parser();
    }

    public function testClassCanBeInstantiated() {
        $this->assertTrue(is_object($this->testClass));
    }

    public function testObjectIsOfCorrectType() {
        $this->assertTrue(get_class($this->testClass) == 'Yugeon\HTML5Parser\Parser');
    }

    public function testCanGetParsedHtmlWithoutChanges()
    {
        $html = '<div><h1>Hello <br /> World</h1></div>';
        $this->testClass->parse($html);
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testCanGetResultsOfParsingAsCollectionOfTags()
    {
        $html = '<div><h1>Hello <br /> World</h1></div>';
        $this->testClass->parse($html);
        $this->assertCount(5, $this->testClass->getNodes());
    }

    public function testCanConsiderComments()
    {
        $html = '<div><!--h1>Hello <br /> World</h1--></div>';
        $this->testClass->parse($html);
        $this->assertCount(3, $this->testClass->getNodes());
    }

    public function testCanConsiderScriptsAndTemplates()
    {
        $html = '<div>
                    <p>Hello</p>
                    <!-- comment -->
                    <script type="text/javascript">
                        var a = "<body></body>";
                    </script>
                    <template id="abc">
                        <div>hello</div>
                    </template>
                </div>';
        $this->testClass->parse($html);
        $this->assertCount(9, $this->testClass->getNodes());
    }

    public function testCanGetWorkTime()
    {
        $html = '<div><h1>Hello <br /> World</h1></div>';
        $this->testClass->parse($html);
        $this->assertTrue(is_float($this->testClass->getWorkTime()));
    }

    public function testNodesMustBeCollection()
    {
        $html = '<div><h1>Hello <br /> World</h1></div>';
        $this->testClass->parse($html);
        $this->assertTrue(get_class($this->testClass->getNodes()) == 'Yugeon\HTML5Parser\NodeCollection');
    }

    public function _testGetNodeNestingLevel()
    {
        $html = '<div>
                    <p>Hello</p>
                    <!-- comment -->
                    <script type="text/javascript">
                        var a = "<body></body>";
                    </script>
                    <template id="abc">
                        <div>hello</div>
                    </template>
                </div>';
        $this->testClass->parse($html);
        $nodes = $this->testClass->getNodes();
        $this->assertEquals(0, $nodes->item(0)->getLevel()); // <div>
        $this->assertEquals(1, $nodes->item(1)->getLevel()); // <p>
        $this->assertEquals(1, $nodes->item(1)->getLevel()); // </p>
    }

}