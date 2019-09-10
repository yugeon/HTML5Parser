<?php

namespace Yugeon\HTML5Parser;

use Yugeon\HTML5Parser\Dom\DomDocument;
use Yugeon\HTML5Parser\Dom\ElementNode;
use Yugeon\HTML5Parser\Dom\ElementNodeInterface;
use Yugeon\HTML5Parser\Dom\TextNode;
use Yugeon\HTML5Parser\Dom\NodeAttribute;
use Yugeon\HTML5Parser\Dom\CommentNode;

class Parser implements ParserInterface
{
    const REMOVED_SCRIPTS_TEMPLATE = 'XRMG83jy_';

    /** @var DomDocument */
    protected $domDocument = null;

    /**
     * Array of contents from removed scripts
     *
     * @var string[]
     */
    protected $removedScripts = [];

    /** @var float */
    protected $startTime = 0;

    /** @var bool */
    protected $isAutoescapeTextNodes  = false;

    public $isDebug = false;

    /**
     * {@inheritDoc}
     */
    public function parse($html)
    {
        $this->clear();
        $this->startTime = microtime(true);

        $this->preserveDocumentWhitespace($html);
        $html = $this->preserveScripts($html);

        // tags + string tag
        // (?:(?<comment><!--.*?-->)|(?<node><\s*(?<end1>/)?(?<tag>\s*[^\s/>]+)(?<attr>(?:[^=/>]+(?:=\s*(?:".*?"|\'.*?\'|[^>\s]+)?)?)*)(?<end2>\s*/)?\s*>))(?<text>[^<]+)?

        // tags + string tag + attr
        // (?:(?<comment><!--.*?-->)|(?<node><\s*(?<end1>/)?(?<tag>\s*[^\s/>]+)(?<attr>(?:(?<ws>\s+)?[^>\w]*(?<name>[^\s=>]+)?(?:(?<sign>\s*=\s*)(?:"(?<value1>.*?)"|'(?<value2>.*?)'|(?<value3>[^>\s]+))?)?)*)(?<end2>\s*/)?\s*>))(?<text>[^<]+)?
        if (false !== preg_match_all(
            '#(?:(?<comment><!--.*?-->)|(?<node><(?<end1>/)?[^\w<>]*(?<tag>[^\s>/]+)\s*(?:[^=>]+(?:=\s*(?:".*?"|\'.*?\'|[^>\s]+)?)?)*\s*/?>))(?<text>[^<]+)?#is',
            $html,
            $matches,
            PREG_SET_ORDER
        )) {
            if (isset($matches)) {
                $this->buildNodesTree($matches);
            }
        }

        return $this->getDomDocument();
    }

    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        $this->domDocument = null;
        $this->removedScripts = [];
        $this->startTime = 0;
        $this->preservedDocumentWhitespace = '';
    }

    /**
     * Building a node tree.
     *
     * @param array $matches
     * @return void
     */
    protected function buildNodesTree($matches = [])
    {
        $root = $this->getDomDocument();
        $parentNode = $root;

        foreach ($matches as $match) {
            if (!empty($match['node'])) {
                if (!empty($match['end1']) && !empty($match['tag'])) {
                    // ignore close tag for other tag
                    if ($parentNode && $parentNode instanceof ElementNodeInterface && $parentNode->tagName === $match['tag']) {
                        $parentNode->addEndTag($match['node']);
                        $parentNode = $parentNode->parentNode;
                    } else {
                        // try find parent for this tag it this tag not self closing
                        $isFind = false;
                        try {
                            $tempNode = new ElementNode($match['tag']);
                            if (!$tempNode->isSelfClosingTag()) {
                                if ($parentNode && $parentNode->parentNode && $parentNode->parentNode instanceof ElementNodeInterface && $parentNode->parentNode->tagName === $match['tag']) {
                                    $parentNode = $parentNode->parentNode;
                                    $parentNode->addEndTag($match['node']);
                                    $parentNode = $parentNode->parentNode;
                                    $isFind = true;
                                }
                            }
                        } catch (\Exception $e) {
                        }

                        if (!$isFind) {
                            // add as text node
                            $isDoEncoding = false;
                            $textNode = new TextNode($match['node'], $isDoEncoding);
                            $parentNode->appendChild($textNode);
                        }
                    }

                    $node = null;
                } else {
                    $node = $this->parseStringTag($match['node'], $parentNode);
                }
            } else if (isset($match['comment'])) {
                $node = $this->parseCommentTag($match['comment'], $parentNode);
            } else {
                // TODO: warning unusual situation
                continue;
            }

            if (!is_null($node) && $node instanceof \DOMNode) {

                if ($node->isSelfClosingTag() || $node->isComment()) {
                    $parentNode = $node->parentNode;
                } else if ($node->isEndTag) {
                    $parentNode = $parentNode->parentNode;
                } else {
                    $parentNode = $node;
                }
            }

            if (isset($match['text']) && strlen($match['text']) > 0) {
                $textContent = $match['text'];
                $isDoEncoding = $this->getAutoescapeTextNodes();

                if (($parentNode instanceof \DOMElement) && preg_match('#script|template|style#i', $parentNode->tagName)) {
                    $isDoEncoding = false;
                    $hash = str_replace(self::REMOVED_SCRIPTS_TEMPLATE, '', $match['text']);
                    if (isset($this->removedScripts[$hash])) {
                        $textContent = $this->removedScripts[$hash];
                    }
                }

                $textNode = new TextNode($textContent, $isDoEncoding);
                if ($parentNode) {
                    $parentNode->appendChild($textNode);
                } else {
                    if ($this->isDebug) {
                        echo 'parser.php:' . __LINE__ . ' ' . $textNode . "'\n";
                    }
                }

            }
        }
    }

    /**
     * Parses a begin string tag into a node object and inserts it into the node tree.
     *
     * @param string $stringValue
     * @param \DOMNode $parentNode
     * @return ElementNode|null
     */
    protected function parseStringTag($stringValue, $parentNode)
    {
        if (empty($stringValue)) {
            return null;
        }

        if (false !== preg_match(
            '#<(?<tag>\s*[^\s>/]+)(?<attr>(?:[^=/>]+(?:=\s*(?:".*?"|\'.*?\'|[^>\s]+)?)?)*)(?<end2>\s*/)?>#is',
            $stringValue,
            $matches
        )) {
            if (isset($matches['tag'])) {
                if (0 === strcasecmp('!doctype', $matches['tag'])) {
                    $implementation = new \DOMImplementation();
                    $doctype = isset($matches['attr']) ? substr($matches['attr'], 1) : '';
                    $this->getDomDocument()->appendChild($implementation->createDocumentType($doctype));
                    return null;
                }

                try {
                    $node = new ElementNode($matches['tag']);
                } catch (\Exception $e) {
                    // try add as text node
                    $isDoEncoding = false;
                    $node = new TextNode($stringValue, $isDoEncoding);
                    $parentNode->appendChild($node);
                    return null;
                }

                if ($parentNode) {
                    $parentNode->appendChild($node);
                } else {
                    if ($this->isDebug) {
                        echo 'parser.php:' . __LINE__ . ' ' . $stringValue . "\n";
                    }
                }


            } else {
                return null;
            }

            if (!empty($matches['end2'])) {
                $node->setSelfClosing(true);
            }

            if (isset($matches['attr'])) {
                if (preg_match('#^\s*$#s', $matches['attr'])) {
                    $node->setWhitespaceAfter($matches['attr']);
                } else {
                    $attributesArr = $this->parseAttributes($matches['attr'], $node);
                    foreach ($attributesArr as $attr) {
                        /** @var ElementNode $node */
                        $node->setAttributeNode($attr);
                    }
                }
            }

            return $node;
        }
    }

    /**
     * Parses a string containing html attributes and return array of attribute nodes.
     * @example 'id="abc" class="red" href="//site.domain/" disabled'
     *
     * @param string $attrStr
     * @param ElementNode $parentNode
     * @return NodeAttribute[]
     */
    public function parseAttributes($attrStr, $parentNode)
    {
        if (empty($attrStr)) {
            return [];
        }

        if (false !== preg_match_all(
            '#(?<ws>\s+)?[\W]*(?<name>[^\s=]+)?(?:(?<sign>\s*=\s*)(?:"(?<value1>.*?)"|\'(?<value2>.*?)\'|(?<value3>[^>\s]+))?)?#is',
            $attrStr,
            $matches,
            PREG_SET_ORDER
        )) {
            if (isset($matches)) {
                return $this->buildAttributes($matches, $parentNode);
            }
        }
    }

    /**
     * Build attributes collection
     *
     * @param array $attrs
     * @param ElementNode $parentNode
     * @return NodeAttribute[]
     */
    protected function buildAttributes($attrs, $parentNode)
    {
        $attributesArr = [];

        foreach ($attrs as $attr) {
            // process first or last whitespaces
            if (!isset($attr['name'])) {
                if (isset($attr['ws'])) {
                    $parentNode->setWhitespaceAfter($attr['ws']);
                }
                continue;
            }

            $name = $attr['name'];

            $quotesSymbol = '';
            $value = null;
            if (isset($attr['value3'])) {
                $value = $attr['value3'];
                $quotesSymbol = '';
            } else if (isset($attr['value2'])) {
                $value = $attr['value2'];
                $quotesSymbol = '\'';
            } else if (isset($attr['value1'])) {
                $value = $attr['value1'];
                $quotesSymbol = '"';
            }

            $signStr = !empty($attr['sign']) ? $attr['sign'] : null;
            $whitespaceBefore = isset($attr['ws']) ? $attr['ws'] : '';

            // ignore invalid attributes
            try {
                $attributesArr[] = new NodeAttribute($name, $value, $whitespaceBefore, $signStr, $quotesSymbol, $this->getAutoescapeTextNodes());
            } catch (\Exception $e) {
                if ($this->isDebug) {
                    echo 'parser.php:' . __LINE__; print_r($attr); echo "\n";
                }
            }

        }

        return $attributesArr;
    }

    /**
     * Parses a string comment tag into a comment object and inserts it into the node tree.
     *
     * @param string $stringValue
     * @param \DOMNode $parentNode
     * @return ElementNode|null
     */
    protected function parseCommentTag($stringValue, $parentNode)
    {
        if (empty($stringValue)) {
            return null;
        }

        if (false !== preg_match('#<(?<tag>!--)(?<comment>.*?)-->#is', $stringValue, $matches)) {

            if (isset($matches['comment'])) {
                $node = new CommentNode($matches['comment']);
                $parentNode->appendChild($node);
            } else {
                // TODO: not comment
                //throw new \Exception("Seems [{$stringValue}] not comment");
                return null;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getHtml()
    {
        return $this->getDomDocument()->getHtml();
    }

    /**
     * Preserves whitespace before the first tag.
     *
     * @param string $html
     * @return void
     */
    protected function preserveDocumentWhitespace($html)
    {
        $firstTagPos = strpos($html, '<');
        if (false !== $firstTagPos && 0 !== $firstTagPos) {
            $this->getDomDocument()->setPreservedDocumentWhitespace(substr($html, 0, $firstTagPos));
        } else {
            $this->getDomDocument()->setPreservedDocumentWhitespace('');
        }
    }

    /**
     * Preserves the content of scripts and templates tags.
     *
     * @param string $html Input html.
     * @return string HTML with simplified content of script and template tags.
     */
    protected function preserveScripts($html)
    {
        $removedScripts = [];
        $scriptsCnt = 0;
        $template = static::REMOVED_SCRIPTS_TEMPLATE;

        // need process larg content of script with str* functions
        $startScriptOffset = 0;
        while (preg_match('#<!--.*?-->|<script\b[^>]*>#is', $html, $startScript, PREG_OFFSET_CAPTURE, $startScriptOffset)) {

            if (!isset($startScript[0]) || !isset($startScript[0][0]) || !isset($startScript[0][1])) {
                break;
            }

            $startScriptOffset = $startScript[0][1] + strlen($startScript[0][0]);

            // skip comments
            if (0 === strpos($startScript[0][0], '<!--')) {
                continue;
            }

            $endScriptOffset = stripos($html, '</script>', $startScriptOffset);

            if (false === $endScriptOffset) {
                // not closed script tag
                break;
            }

            $scriptContent = substr($html, $startScriptOffset, $endScriptOffset - $startScriptOffset);

            if (empty($scriptContent)) {
                // not preserve empty script content
                continue;
            }

            $scriptHash = md5($scriptContent);
            $html = substr_replace($html, $template . $scriptHash, $startScriptOffset, $endScriptOffset - $startScriptOffset);

            $removedScripts[$scriptHash] = $scriptContent;
            $scriptsCnt++;
            $startScriptOffset = $endScriptOffset + strlen('</script>');
        }

        $html = preg_replace_callback("#<!--.*?-->|<(?<tag>template|style)\b([^>]*)>(.*?)</\\1>#is", function ($matches) use ($template, &$scriptsCnt, &$removedScripts) {
            if (empty($matches['tag'])) {
                return $matches[0];
            }
            if (empty($matches[3])) {
                return $matches[0];
            }
            $hash = md5($matches[3]);
            $result = "<{$matches[1]}{$matches[2]}>{$template}{$hash}</{$matches[1]}>";
            $removedScripts[$hash] = $matches[3];
            $scriptsCnt++;
            return $result;
        }, $html);
        $this->removedScripts = $removedScripts;

        return $html;
    }

    public function getWorkTime()
    {
        return microtime(true) - $this->startTime;
    }

    /**
     * {@inheritDoc}
     */
    public function getDomDocument()
    {
        if (is_null($this->domDocument)) {
            $this->domDocument = new DomDocument('1.0', 'UTF-8');
        }

        return $this->domDocument;
    }

    /**
     * {@inheritDoc}
     */
    public function setAutoescapeTextNodes($isAutoescape)
    {
        $this->isAutoescapeTextNodes = $isAutoescape;
    }

    /**
     * {@inheritDoc}
     */
    public function getAutoescapeTextNodes()
    {
        return $this->isAutoescapeTextNodes;
    }
}
