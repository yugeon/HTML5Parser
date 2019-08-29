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
        $this->assertCount(2, $this->testClass->getNodes());
    }

    public function testCanGetResultsOfParsingAsCollectionOfTags()
    {
        $html = '<div><h1>Hello <br /> World</h1></div>';
        $this->testClass->parse($html);
        $this->assertCount(2, $this->testClass->getNodes());
    }

    public function testCanConsiderComments()
    {
        $html = '<div><!--h1>Hello <br /> World</h1--></div>';
        $this->testClass->parse($html);
        $this->assertCount(2, $this->testClass->getNodes());

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
        $this->assertCount(2, $this->testClass->getNodes());
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testCommentsNotCreateChildNodes()
    {
        $html = '<!--[if gt IE 8]><!--><html lang="en"><!--<![endif]-->
                 </html>';
        $this->testClass->parse($html);
        $this->assertCount(3, $this->testClass->getNodes());
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
        $this->assertEquals(1, $div->getChilds()->item(1)->getLevel()); // </p>
        $this->assertEquals(1, $div->getChilds()->item(2)->getLevel()); // <!-- comment -->
    }

    public function testMustCorrectParseTagsWithAnyAttributes()
    {
        $html = '<div class="red>green">Hello</div>';
        $this->testClass->parse($html);

        $this->assertCount(2, $this->testClass->getNodes());

        $this->assertEquals('div', $this->testClass->getNodes()->item(0)->getTagName());
        $this->assertTrue($this->testClass->getNodes()->item(1)->isEndTag);
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