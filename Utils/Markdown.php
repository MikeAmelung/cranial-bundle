<?php

namespace MikeAmelung\CranialBundle\Utils;

class Markdown
{
    private $parser;

    public function __construct()
    {
        $this->parser = new \Parsedown();
    }

    public function toHtml(string $text): string
    {
        return $this->parser->text($text);
    }

    public function toHtmlInline(string $text): string
    {
        return $this->parser->line($text);
    }
}
