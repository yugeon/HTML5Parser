<?php

namespace Yugeon\HTML5Parser;

use Yugeon\HTML5Parser\NodeCollection;
use Yugeon\HTML5Parser\Node;

class Parser
{
    const REMOVED_SCRIPTS_TEMPLATE = 'XRMG83jy_';

    /** @var NodeCollection */
    protected $nodes = null;

     /**
     * Array of contents from removed scripts
     *
     * @var string[]
     */
    protected $removedScripts = [];

    /** @var float */
    protected $startTime = 0;

    /** @var string */
    protected $preservedDocumentWhitespaces = '';

    public function parse($html)
    {
        $this->startTime = microtime(true);

        $this->preservDocumentWhitespaces($html);
        $html = $this->preserveScripts($html);

        if (false !== preg_match_all('#(?:(?<comment><!--.*?-->)|(?<node><(?:[^\'">]+|".*?"|\'.*?\')+>))(?<html>[^<]*)#is', $html, $matches, PREG_SET_ORDER)) {
        // if (false !== preg_match_all('#(?:<!--.*?-->|(?:<(?:[^\'">]+|".*?"|\'.*?\')+>))[^<]*#is', $html, $matches)) {
        // if (false !== preg_match_all('#(?:<!--.*?-->|<[^>]+>)[^<]*#is', $html, $matches)) {
            if (isset($matches)) {
                $this->buildNodesTree($matches);
            }
        }

        return $this;
    }

    public function buildNodesTree($nodes = [])
    {
        $root = new Node('<root>');
        $root->setLevel(-1);
        $prevNode = $root;

        foreach ($nodes as $node) {
            $isComment = false;
            if (!empty($node['node'])) {
                $nodeStr = $node['node'];
            } else if(isset($node['comment'])) {
                $nodeStr = $node['comment'];
                $isComment = true;
            } else {
                // TODO: warning unusual situation
                continue;
            }

            $nodeStr .= isset($node['html'])? $node['html'] : '';

            $node = new Node($nodeStr, $isComment);

            if ($node instanceof Node) {

                if ($node->isStartTag) {
                    $prevNode->addNode($node);
                }

                if ($node->isEndTag) {
                    $prevNode->getParent()->addNode($node);
                }

                if ($node->isEndTag || $node->isSelfClosingTag() || $node->isComment()) {
                    $prevNode = $node->getParent();
                } else {
                    $prevNode = $node;
                }
            }
        }

        $this->nodes = $root->getChilds();
    }

    public function getHtml()
    {
        $html = $this->preservedDocumentWhitespaces;
        foreach ($this->nodes as $node) {
            $html .= $node->getHtml();
        }

        return $this->restoreScripts($html);
    }

    public function getNodes()
    {
        return $this->nodes;
    }

    protected function preservDocumentWhitespaces($html) {
        $firstTagPos = strpos($html, '<');
        if (false !== $firstTagPos && 0 !== $firstTagPos) {
            $this->preservedDocumentWhitespaces = substr($html, 0, $firstTagPos);
        } else {
            $this->preservedDocumentWhitespaces = '';
        }
    }

    protected function preserveScripts($html)
    {
        $removedScripts = [];
        $scriptsCnt = 0;
        $template = static::REMOVED_SCRIPTS_TEMPLATE;
        $html = preg_replace_callback("#<(script|template)\b([^>]*)>(.*?)</\\1>#is", function ($matches) use ($template, &$scriptsCnt, &$removedScripts) {
            $hash = md5($matches[3]);
            $result = "<{$matches[1]}{$matches[2]}>{$template}{$hash}</{$matches[1]}>";
            $removedScripts[$hash] = $matches[3];
            $scriptsCnt++;
            return $result;
        }, $html);
        $this->removedScripts = $removedScripts;

        return $html;
    }

    protected function restoreScripts($html)
    {
        $search = [];
        $hashes = array_keys($this->removedScripts);
        foreach ($hashes as $hash) {
            $search[] = static::REMOVED_SCRIPTS_TEMPLATE . $hash;
        }

        return str_replace($search, $this->removedScripts, $html);
    }

    public function getWorkTime()
    {
        return microtime(true) - $this->startTime;
    }
}
