<?php

namespace MikeAmelung\CranialBundle\Utils;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\InlinesOnly\InlinesOnlyExtension;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\MarkdownConverter;

class Markdown
{
    private $converter;
    private $inlineConverter;

    public function __construct()
    {
        $environment = new Environment(['allow_unsafe_links' => false]);
        $environment->addExtension(new AttributesExtension());
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());

        $this->converter = new MarkdownConverter($environment);

        $inlineEnvironment = new Environment(['allow_unsafe_links' => false]);
        $inlineEnvironment->addExtension(new InlinesOnlyExtension());

        $this->inlineConverter = new MarkdownConverter($inlineEnvironment);
    }

    public function toHtml(?string $text): string
    {
        return $this->converter->convertToHtml($text);
    }

    public function toHtmlInline(?string $text): string
    {
        return $this->inlineConverter->convertToHtml($text);
    }
}
