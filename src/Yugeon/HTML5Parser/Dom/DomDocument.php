<?php

namespace Yugeon\HTML5Parser\Dom;

class DomDocument extends \DOMDocument implements DomDocumentInterface
{

    /**
     * Whitespace before the first tag.
     *
     * @var string
     */
    protected $preservedDocumentWhitespace = '';

    /**
     * A collection of references to objects with nodes.
     * It is necessary to prevent the loss of user nodes in the depths of DOMDocument.
     *
     * @var NodeInterface[]
     */
    protected $_nodes = [];

    /**
     * {@inheritDoc}
     */
    public function appendChild($node)
    {
        $this->_nodes[] = $node;
        return parent::appendChild($node);
    }

    /**
     * {@inheritDoc}
     */
    public function setPreservedDocumentWhitespace($whitespace)
    {
        $this->preservedDocumentWhitespace = $whitespace;
    }

    /**
     * {@inheritDoc}
     */
    public function getPreservedDocumentWhitespace()
    {
        return $this->preservedDocumentWhitespace;
    }

    /**
     * {@inheritDoc}
     */
    public function getHtml($node = null)
    {
        if (!is_null($node)) {
            $html = $node->getHtml();
        } else {

            $html = $this->preservedDocumentWhitespace;
            /** @var \DOMNode $node */
            foreach ($this->childNodes as $node) {
                if (XML_DOCUMENT_TYPE_NODE === $node->nodeType) {
                    $html .= $this->saveXML($node);
                } else {
                    $html .= $node->getHtml();
                }
            }
        }

        return $html;
    }
}
