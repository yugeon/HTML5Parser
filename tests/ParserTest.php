<?php

namespace Yugeon\HTML5Parser\Tests;

use PHPUnit\Framework\TestCase;
use Yugeon\HTML5Parser\Parser;

class ParserTest extends TestCase {

    private $testClass;
    static private $testContent;

    static function setUpBeforeClass() {
        self::$testContent = file_get_contents(__DIR__ . '/../assets/6pm.html');
    }

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

}