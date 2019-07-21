<?php

namespace Yugeon\HTML5Parser\Tests\Functional;

use PHPUnit\Framework\TestCase;
use Yugeon\HTML5Parser\Parser;
use DOMDocument;

class ParserTest extends TestCase {

    private $testClass;
    static private $testContent;

    static function setUpBeforeClass() {
        self::$testContent = file_get_contents(__DIR__ . '/../assets/dolls.html');
    }

    // function setUp() {
    //     $this->testClass = new Parser();
    // }

    public function testDomDocumentParserOnRealPage()
    {
        $start = microtime(true);
        $internalErrors = libxml_use_internal_errors(true);
        $disableEntities = libxml_disable_entity_loader(true);

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = true;
        $dom->formatOutput = false;
        $dom->validateOnParse = false;
        $dom->substituteEntities = false;
        $dom->resolveExternals = false;

        set_error_handler(function () {
            throw new \Exception();
        });

        $content = static::$testContent;
        // Preserve scripts
        $removedScripts = [];
        $scriptsCnt = 0;
        $template = 'asdfasdf_';
        $content = preg_replace_callback("#<(script|template)\b([^>]*)>(.*?)</\\1>#is", function ($matches) use ($template, &$scriptsCnt, &$removedScripts) {
            $hash = md5($matches[3]);
            $result = "<{$matches[1]} {$matches[2]}>{$template}{$hash}</{$matches[1]}>";
            $removedScripts[$hash] = $matches[3];
            $scriptsCnt++;
            return $result;
        }, $content);

        // Preserve html entities
        $content = preg_replace('/&(#?[a-zA-Z0-9]*);/', 'asdfasdf' . '-$1-end', $content);

        try {
            // Convert charset to HTML-entities to work around bugs in DOMDocument::loadHTML()
            $content = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');
        } catch (\Exception $e) { }


        @$dom->loadHTML(static::$testContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        restore_error_handler();

        libxml_use_internal_errors($internalErrors);
        libxml_disable_entity_loader($disableEntities);

        dump('DOMDocument', microtime(true) - $start);
    }

    public function _testParserWarmUp()
    {
        $this->testClass = new Parser();
        $this->testClass->parse('<div>hello</div>');
        // dump('this', $this->testClass->getWorkTime());
    }


    public function testParserOnRealPage()
    {
        $this->testClass = new Parser();
        $this->testClass->parse(static::$testContent);
        dump('This Parser', $this->testClass->getWorkTime());
    }

}