<?php

namespace Yugeon\HTML5Parser;

class Parser
{
    const REMOVED_SCRIPTS_TEMPLATE = 'XRMG83jy_';

    /** @var \DOMDocument */
    protected $domDocument = null;

    /**
     * Array of contents from removed scripts
     *
     * @var string[]
     */
    protected $removedScripts = [];

    /** @var float */
    protected $startTime = 0;

    /**
     * Parse input html.
     *
     * @param string $html Input html.
     * @return \DOMDocument
     */
    public function parse($html)
    {
        $this->clear();
        $this->startTime = microtime(true);

        $this->preserveDocumentWhitespace($html);
        $html = $this->preserveScripts($html);

        if (false !== preg_match_all(
            '#(?:(?<comment><!--.*?-->)|(?<node><(?<end1>/)?(?:[^\'">]+|".*?"|\'.*?\')+>))(?<text>[^<]*)#is',
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
     * Clear the parser for reuse.
     *
     * @return void
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
                if (!empty($match['end1'])) {
                    if ($parentNode instanceof ElementNodeInterface) {
                        $parentNode->addEndTag($match['node']);
                    }
                    $parentNode = $parentNode->parentNode;
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

                // $parentNode->appendChild($node);

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

                if (($parentNode instanceof \DOMElement) && preg_match('#script|template#i', $parentNode->tagName)) {
                    $hash = str_replace(self::REMOVED_SCRIPTS_TEMPLATE, '', $match['text']);
                    if (isset($this->removedScripts[$hash])) {
                        $textContent = $this->removedScripts[$hash];
                    }
                }

                $textNode = new TextNode($textContent);
                $parentNode->appendChild($textNode);
            }
        }
    }

    /**
     * Parses a string tag into a node object and inserts it into the node tree.
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
            '#<(?<tag>[^!\s>/]+|!doctype)(?<attr>\s+(?:[^\'"/>]+|".*?"|\'.*?\')*)?(?<end2>\s*/)?>#is',
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
                    return null;
                }

                $parentNode->appendChild($node);
            } else {
                return null;
            }

            if (!empty($matches['end2'])) {
                $node->setSelfClosing(true);
            }

            if (isset($matches['attr'])) {
                if (preg_match('#\s*#s', $matches['attr'])) {
                    $node->setWhitespaces($matches['attr']);
                } else {
                    $attributesArr = $this->parseAttributes($matches['attr']);
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
     * @return NodeAttribute[]
     */
    public function parseAttributes($attrStr)
    {
        if (empty($attrStr)) {
            return [];
        }

        if (false !== preg_match_all(
            '#(?<ws>\s+)?(?<name>[^\s=\'"]+)?(?:(?<sign>\s*=\s*)(?:"(?<value1>.*?)"|\'(?<value2>.*?)\'|(?<value3>[^\'">\s]+))?)?#is',
            $attrStr,
            $matches,
            PREG_SET_ORDER
        )) {
            if (isset($matches)) {
                return $this->buildAttributes($matches);
            }
        }
    }

    /**
     * Build attributes collection
     *
     * @param array $attrs
     * @return NodeAttribute[]
     */
    protected function buildAttributes($attrs)
    {
        $attributesArr = [];

        foreach ($attrs as $attr) {
            // process first or last whitespaces
            if (!isset($attr['name'])) {
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
            $whitespaceBefore = !empty($attr['ws']) ? $attr['ws'] : '';

            // $this->addAttribute(new $this->nodeAttributeClassName($name, $value, $whitespaceBefore, $signStr, $quotesSymbol));
            $attributesArr[] = new NodeAttribute($name, $value, $whitespaceBefore, $signStr, $quotesSymbol);
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
     * The only way to get the correct HTML.
     *
     * @return string
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
        $html = preg_replace_callback("#<!--.*?-->|<(?<tag>script|template)\b([^>]*)>(.*?)</\\1>#is", function ($matches) use ($template, &$scriptsCnt, &$removedScripts) {
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
     * Reference to the DomDocument object of the current parsing result.
     * Note that it is better to use the getHtml() method instead saveHtml().
     *  @see DomDocument::getHtml()
     *
     * @return DomDocument
     */
    public function getDomDocument()
    {
        if (is_null($this->domDocument)) {
            $this->domDocument = new DomDocument('1.0', 'UTF-8');
        }

        return $this->domDocument;
    }
}
