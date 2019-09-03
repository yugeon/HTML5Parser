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

    public function testResultOfParseMustBeDomDocument()
    {
        $html = '<div><h1>Hello <br /> World</h1></div>';
        $this->assertInstanceOf(\DOMDocument::class, $this->testClass->parse($html));
    }

    public function testCanGetParseResultAsDomDocument()
    {
        $html = '<div><h1>Hello <br /> World</h1></div>';
        $this->testClass->parse($html);

        $this->assertInstanceOf(\DOMDocument::class, $this->testClass->getDomDocument());
    }

    public function testCanGetParsedHtmlWithoutChanges()
    {
        $html = '<div><h1>Hello <br/> World</h1></div>';
        $this->testClass->parse($html);
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testCanConsiderComments()
    {
        $html = '<div><!--h1>Hello <br /> World</h1--></div>';
        $this->testClass->parse($html);
        $this->assertEquals($html, $this->testClass->getHtml());
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

    public function testCanConsiderScriptsAndTemplates()
    {
        $html = '<div>
                    <p>Hello</p>
                    <!-- comment -->
                    <script>
                        var a = "<body></body>";
                    </script>
                    <template>
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
        $this->assertEquals(1, $this->testClass->getDomDocument()->childNodes->length);
    }

    public function testCommentsNotCreateChildNodes()
    {
        $html = '<!--[if gt IE 8]><!--><html><!--<![endif]-->
                 </html>';
        $this->testClass->parse($html);
        $this->assertEquals($html, $this->testClass->getHtml());
        $this->assertEquals(2, $this->testClass->getDomDocument()->childNodes->length);
    }

    public function testCanGetWorkTime()
    {
        $html = '<div><h1>Hello <br /> World</h1></div>';
        $this->testClass->parse($html);
        $this->assertTrue(is_float($this->testClass->getWorkTime()));
    }


    public function testTextNodeMustCreateSeparateNode()
    {
        $html = '<div>Hello</div>World';
        $this->testClass->parse($html);
        $this->assertEquals(2, $this->testClass->getDomDocument()->childNodes->length);
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testMustCorrectParseTagsWithAnyAttributes()
    {
        $html = '<div class="red>green">Hello</div>';
        $this->testClass->parse($html);

        $this->assertEquals(1, $this->testClass->getDomDocument()->childNodes->length);

        $this->assertEquals('div', $this->testClass->getDomDocument()->childNodes->item(0)->tagName);
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testMustPreservWhitespacesBeforeDoctype()
    {
        $html = '
            <!DOCTYPE html>
            <html></html>
        ';
        $this->testClass->parse($html);
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testMustClearDocumentBeforeParse()
    {
        $html = '<div class="red">Hello</div>';
        $this->testClass->parse($html);

        $html = '<div class="red">Hello</div>';
        $this->testClass->parse($html);
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testCanParseAttributes()
    {
        $html = '<div id="2" class="red" custom-attr=\'hello world\'>';
        $this->testClass->parse($html);
        $this->assertEquals($html, $this->testClass->getHtml());
    }


    public function testCanParseOneTag()
    {
        $html = '<div>';
        $this->testClass->parse($html);
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testCanParseComments()
    {
        $html = '<!-- abrakadabra -->';
        $this->testClass->parse($html, true);
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testCanParseDoctype()
    {
        $html = '<!DOCTYPE html>';
        $this->testClass->parse($html);
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testCanParseCommentsBeforeDoctype()
    {
        $html = '<!-- comment -->
                <!DOCTYPE html>';
        $this->testClass->parse($html);
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testCanParseTagsWithWhitespaces()
    {
        $html = '<div  id="abc">';
        $this->testClass->parse($html);
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testCanParseSelfClosingTags()
    {
        $html = '<br />';
        $this->testClass->parse($html);
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testCanRestoreOriginalTagWithWhitespaces()
    {
        $html = '<div  id="abc" >';
        $this->testClass->parse($html);
        $this->assertEquals($html, $this->testClass->getHtml());
    }

    public function testIgnoreNotHtmlTags()
    {
        $html = '<?xml version="1.0" encoding="UTF-8"?>';
        $this->testClass->parse($html);
        $this->assertEquals('', $this->testClass->getHtml());
    }

    public function testCanParseCustomTags()
    {
        $html = '<custom-tag>some text</custom-tag>
                <yet-another-custom-tag />
                <and-some-one>
                ';
        $this->testClass->parse($html);
        $this->assertEquals($html, $this->testClass->getHtml());
    }
}
