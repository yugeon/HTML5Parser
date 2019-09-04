<?php

namespace Yugeon\HTML5Parser\Tests;

use PHPUnit\Framework\TestCase;
use Yugeon\HTML5Parser\DomDocumentInterface;
use Yugeon\HTML5Parser\Parser;
use Yugeon\HTML5Parser\ElementNode;
use Yugeon\HTML5Parser\NodeAttribute;

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

    public function testMustBeEmptyResultIfParseNotCalled()
    {
        $this->assertEmpty($this->testClass->getHtml());
    }

    public function testResultOfParseMustBeDomDocument()
    {
        $html = '<div><h1>Hello <br /> World</h1></div>';
        $this->assertInstanceOf(\DOMDocument::class, $this->testClass->parse($html));
    }

    public function testResultOfParseMustBeDomDocumentInterface()
    {
        $html = '<div><h1>Hello <br /> World</h1></div>';
        $this->assertInstanceOf(DomDocumentInterface::class, $this->testClass->parse($html));
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
        $domDocument = $this->testClass->parse($html);
        $this->assertEquals($html, $domDocument->getHtml());
    }

    public function testCanConsiderComments()
    {
        $html = '<div><!--h1>Hello <br /> World</h1--></div>';
        $domDocument = $this->testClass->parse($html);
        $this->assertEquals($html, $domDocument->getHtml());
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
        $domDocument = $this->testClass->parse($html);
        $this->assertEquals($html, $domDocument->getHtml());
    }

    public function testCanConsiderScriptsAndTemplates()
    {
        $html = '<div>
                    <p>Hello</p>
                    <!-- comment -->
                    <script type="text/html">
                        var a = "<body></body>";
                    </script>
                    <template id="abc">
                        <div>hello</div>
                    </template>
                </div>';
        $domDocument = $this->testClass->parse($html);
        $this->assertEquals($html, $domDocument->getHtml());
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
        $domDocument = $this->testClass->parse($html);
        $this->assertEquals($html, $domDocument->getHtml());
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
        $domDocument = $this->testClass->parse($html);
        $this->assertEquals($html, $domDocument->getHtml());
        $this->assertEquals(2, $this->testClass->getDomDocument()->childNodes->length);
    }

    public function testMustCorrectParseTagsWithAnyAttributes()
    {
        $html = '<div class="red>green">Hello</div>';
        $domDocument = $this->testClass->parse($html);
        $this->assertEquals($html, $domDocument->getHtml());

        $this->assertEquals(1, $this->testClass->getDomDocument()->childNodes->length);

        $this->assertEquals('div', $this->testClass->getDomDocument()->childNodes->item(0)->tagName);
    }

    public function testMustPreservWhitespacesBeforeDoctype()
    {
        $html = '
            <!DOCTYPE html>
            <html></html>
        ';
        $domDocument = $this->testClass->parse($html);
        $this->assertEquals($html, $domDocument->getHtml());
    }

    public function testMustClearDocumentBeforeParse()
    {
        $html = '<div class="red">Hello</div>';
        $this->testClass->parse($html);

        $html = '<div class="red">Hello</div>';
        $domDocument = $this->testClass->parse($html);
        $this->assertEquals($html, $domDocument->getHtml());
    }

    public function testCanParseAttributes()
    {
        $html = '<div id="2" class="red" custom-attr=\'hello world\'>';
        $domDocument = $this->testClass->parse($html);
        $this->assertEquals($html, $domDocument->getHtml());
    }


    public function testCanParseOneTag()
    {
        $html = '<div>';
        $domDocument = $this->testClass->parse($html);
        $this->assertEquals($html, $domDocument->getHtml());
    }

    public function testCanParseComments()
    {
        $html = '<!-- abrakadabra -->';
        $domDocument = $this->testClass->parse($html);
        $this->assertEquals($html, $domDocument->getHtml());
    }

    public function testCanParseDoctype()
    {
        $html = '<!DOCTYPE html>';
        $domDocument = $this->testClass->parse($html);
        $this->assertEquals($html, $domDocument->getHtml());
    }

    public function testCanParseCommentsBeforeDoctype()
    {
        $html = '<!-- comment -->
                <!DOCTYPE html>';
        $domDocument = $this->testClass->parse($html);
        $this->assertEquals($html, $domDocument->getHtml());
    }

    public function testCanParseTagsWithWhitespaces()
    {
        $html = '<div  id="abc">';
        $domDocument = $this->testClass->parse($html);
        $this->assertEquals($html, $domDocument->getHtml());

        /** @var ElementNode $divEl */
        $divEl = $this->testClass->getDomDocument()->childNodes->item(0);
        /** @var NodeAttribute $idAttr */
        $idAttr = $divEl->getAttributeNode('id');
        $this->assertEquals('  ', $idAttr->getPreservedWhitespace());
    }

    public function testCanParseSelfClosingTags()
    {
        $html = '<br />';
        $domDocument = $this->testClass->parse($html);
        $this->assertEquals($html, $domDocument->getHtml());
        /** @var ElementNode $tag */
        $tag = $domDocument->childNodes->item(0);
        $this->assertEquals(' ', $tag->getWhitespaceAfter());
    }

    public function testCanRestoreOriginalTagWithWhitespaces()
    {
        $html = '<div  id="abc" >';
        $domDocument = $this->testClass->parse($html);
        $this->assertEquals($html, $domDocument->getHtml());

        /** @var ElementNode $divEl */
        $divEl = $this->testClass->getDomDocument()->childNodes->item(0);
        /** @var NodeAttribute $idAttr */
        $idAttr = $divEl->getAttributeNode('id');
        $this->assertEquals('  ', $idAttr->getPreservedWhitespace());
    }

    public function testIgnoreNotHtmlTags()
    {
        $html = '<?xml version="1.0" encoding="UTF-8"?>';
        $domDocument = $this->testClass->parse($html);
        $this->assertEquals('', $domDocument->getHtml());
    }

    public function testIgnoreInvalidTags()
    {
        $html = '< div>hello </div>';
        $domDocument = $this->testClass->parse($html);
        $this->assertEquals('hello ', $domDocument->getHtml());
    }

    public function testCanParseCustomTags()
    {
        $html = '<custom-tag>some text</custom-tag>
                <yet-another-custom-tag>some text</yet-another-custom-tag>
                <and-some-one>some text</and-some-one>';
        $domDocument = $this->testClass->parse($html);
        $this->assertEquals($html, $domDocument->getHtml());
    }

    public function testCanParseSelfClosedCustomTags()
    {
        $html = '<custom-tag/> <yet-another-custom-tag />
                <br>
                <and-some-one />

                ';
        $domDocument = $this->testClass->parse($html);
        $this->assertEquals($html, $domDocument->getHtml());
    }

    public function testCanParseFullHtml()
    {
        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

</body>
</html>
HTML;
        $domDocument = $this->testClass->parse($html);
        $this->assertEquals($html, $domDocument->getHtml());
    }

    public function testCanParseCommentsAfterHtml()
    {
        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body>
</body>
</html>
<!-- comment -->
HTML;
        $domDocument = $this->testClass->parse($html);
        $this->assertEquals($html, $domDocument->getHtml());
    }

    public function testCanParseWhitespaceAfterHtml()
    {
        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body>
</body>
</html>

HTML;
        $domDocument = $this->testClass->parse($html);
        $this->assertEquals($html, $domDocument->getHtml());
    }

    public function testCanParseScriptTags()
    {
        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <script>replaceCurrency();</script>
</head>
<body>
</body>
</html>
HTML;
        $domDocument = $this->testClass->parse($html);
        $this->assertEquals($html, $domDocument->getHtml());
    }

    public function testCanParseEmptyScriptTags()
    {
        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <script type="text/javascript" src="/js/carousel_unminify.js"></script>
    <script type="text/javascript" src="/js/carousel_unminify1.js"></script>
</head>
<body>
</body>
</html>
HTML;
        $domDocument = $this->testClass->parse($html);
        $this->assertEquals($html, $domDocument->getHtml());
    }

    public function testCanParseScriptCdataTags()
    {
        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body>

    <script type="text/javascript">
    //<![CDATA[
    var BLANK_URL = 'https://cdn.com/js/blank.html';
    var BLANK_IMG = 'https://cdn.com/js/spacer.gif';
    //]]>
    </script>
</body>
</html>
HTML;
        $domDocument = $this->testClass->parse($html);
        $this->assertEquals($html, $domDocument->getHtml());
    }

    public function testCanParseScriptCdataCommentTags()
    {
        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body>
    <!--[if lt IE 7]>
    <script type="text/javascript">
    //<![CDATA[
    var BLANK_URL = '';
    var BLANK_IMG = '';
    //]]>
    </script>
    <![endif]-->
    <!--[if gte IE 9 | !IE]><!-->
<script src="jquery-2.1.1.min.js" type="text/javascript"></script>
<!--<![endif]-->
<!--[if lte IE 8]>
<script src="jquery-1.11.1.min.js" type="text/javascript"></script>
<![endif]-->
<!--[if lte IE 8]>
<script src="//cdn.com/media.match.min.js" type="text/javascript"></script>
<script src="//cdnjs.es5-shim.min.js"></script>
<![endif]-->
</body>
</html>
HTML;
        $domDocument = $this->testClass->parse($html);
        $this->assertEquals($html, $domDocument->getHtml());
    }
}
