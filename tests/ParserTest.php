<?php

namespace Yugeon\HTML5Parser\Tests;

use PHPUnit\Framework\TestCase;
use Yugeon\HTML5Parser\Parser;

class ParserTest extends TestCase
{

    private $testClass;

    function setUp()
    {
        $this->testClass = new Parser();
    }

    public function testClassCanBeInstantiated()
    {
        $this->assertTrue(is_object($this->testClass));
    }

    public function testObjectIsOfCorrectType()
    {
        $this->assertTrue(get_class($this->testClass) == 'Yugeon\HTML5Parser\Parser');
    }

    public function testCanGetParsedHtmlWithoutChanges()
    {
        $html = '<div><h1>Hello <br /> World</h1></div>';
        $this->testClass->parse($html);
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testCanGetResultsOfParsingAsCollectionOfNodes()
    {
        $html = '<div><h1>Hello <br /> World</h1></div>';
        $this->testClass->parse($html);
        $this->assertInstanceOf('Yugeon\HTML5Parser\NodeCollection', $this->testClass->getNodes());
    }

    public function testCanConsiderComments()
    {
        $html = '<div><!--h1>Hello <br /> World</h1--></div>';
        $this->testClass->parse($html);

        $divNode = $this->testClass->getNodes()->item(0);
        $commentNode = $divNode->getChilds()->item(0);
        $this->assertEquals('!--', $commentNode->getTagName());
    }

    public function testCanConsiderBreakLineAndWhitspaces()
    {
        $html = '<div>
                    <p>Hello</p>
                    <!-- comment -->
                    <div>
                        World
                    </div>
                </div>';
        $this->testClass->parse($html);
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    // TODO: improve checks
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
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testCloseTagNotCreateNewNode()
    {
        $html = '<div>hello</div>';
        $this->testClass->parse($html);
        $this->assertCount(1, $this->testClass->getNodes());
    }

    public function testCommentsNotCreateChildNodes()
    {
        $html = '<!--[if gt IE 8]><!--><html lang="en"><!--<![endif]-->
                 </html>';
        $this->testClass->parse($html);
        $this->assertCount(2, $this->testClass->getNodes());
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

    public function testMustBeSetRightNestingLevel()
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
        $div = $nodes->item(0);
        $this->assertEquals(0, $div->getLevel()); // <div>
        $this->assertEquals(1, $div->getChilds()->item(0)->getLevel()); // <p>
        $this->assertEquals(1, $div->getChilds()->item(1)->getLevel()); // <!-- comment -->
        $this->assertEquals(1, $div->getChilds()->item(2)->getLevel()); // <!-- script -->
        $this->assertEquals(1, $div->getChilds()->item(3)->getLevel()); // <!-- template -->
    }

    public function testParentNodeOfEndNodeMustBeSameNode()
    {
        $html = '<div>Hello</div>World';
        $this->testClass->parse($html);
        $this->assertEquals(
            $this->testClass->getNodes()->item(0),
            $this->testClass->getNodes()->item(0)->getEndNode()->getParent()
        );
    }

    // TODO: need level?
    public function _testEndNodeLevelMustBeSameAsParentNode()
    {
        $html = '<div>
                    <span>Hello</span>World
                 </div>';
        $this->testClass->parse($html);

        $span = $this->testClass->getNodes()->item(0)->getChilds()->item(0);
        $this->assertEquals(1, $span->getEndNode()->getLevel());
        $span->setLevel(2);
        $this->assertEquals(2, $span->getEndNode()->getLevel());
    }

    public function testEndNodeCannotHaveChilds()
    {
        $html = '<div>Hello</div>World<br />';
        $this->testClass->parse($html);
        $this->assertCount(0, $this->testClass->getNodes()->item(0)->getEndNode()->getChilds());
    }

    public function testMustCorrectParseTagsWithAnyAttributes()
    {
        $html = '<div class="red>green">Hello</div>';
        $this->testClass->parse($html);

        $this->assertCount(1, $this->testClass->getNodes());

        $this->assertEquals('div', $this->testClass->getNodes()->item(0)->getTagName());
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testMustPreservWhitespacesBeforeDoctype()
    {
        $html = '
            <!doctype html>
            <html></html>
        ';
        $this->testClass->parse($html);
        $this->assertEquals($html, $this->testClass->getHtml());
    }
}
