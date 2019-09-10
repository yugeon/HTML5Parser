<?php

namespace Yugeon\HTML5Parser\Dom;

interface DomDocumentInterface
{
    /**
     * Preserve whitespace before the first tag.
     *
     * @param string $whitespace
     * @return void
     */
    public function setPreservedDocumentWhitespace($whitespace);

    /**
     * Getting whitespace before the first tag.
     *
     * @return string
     */
    public function getPreservedDocumentWhitespace();

    /**
     * The only way to get the correct HTML.
     *
     * @return string
     */
    public function getHtml();
}
