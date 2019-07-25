<?php

namespace Yugeon\HTML5Parser;

use Yugeon\HTML5Parser\NodeCollection;
use Yugeon\HTML5Parser\Node;

class Parser
{
    const REMOVED_SCRIPTS_TEMPLATE = 'XRMG83jy_';

    /** @var string */
    protected $html = '';

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

    public function parse($html)
    {
        $this->startTime = microtime(true);

        $this->html = $html;

        $html = $this->preserveScripts($html);

        if (false !== preg_match_all('#(?:<!--.*?-->|<[^>]+>)[^<]*#is', $html, $matches)) {
            if (isset($matches[0])) {
                $this->buildNodesTree($matches[0]);
            }
        }

        return $this;
    }

    public function buildNodesTree($nodes = [])
    {
        $root = new Node('<root>');
        $prevNode = $root;

        foreach ($nodes as $node) {
            $node = new Node($node);

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
        $html = '';
        foreach ($this->nodes as $node) {
            $html .= $node->getHtml();
        }

        return $this->restoreScripts($html);
    }

    public function getNodes()
    {
        return $this->nodes;
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
