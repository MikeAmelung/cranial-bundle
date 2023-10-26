<?php

namespace MikeAmelung\CranialBundle\Utils;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Embed\Bridge\OscaroteroEmbedAdapter;
use League\CommonMark\Extension\Embed\EmbedExtension;
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
        $config = [
            'allow_unsafe_links' => false,
            'embed' => [
                'adapter' => new OscaroteroEmbedAdapter(),
                'allowed_domains' => ['youtube.com', 'vimeo.com'],
                'fallback' => 'link',
            ],
        ];

        $environment = new Environment($config);

        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->addExtension(new AttributesExtension());
        $environment->addExtension(new EmbedExtension());

        $this->converter = new MarkdownConverter($environment);

        $inlineEnvironment = new Environment($config);
        $inlineEnvironment->addExtension(new InlinesOnlyExtension());

        $this->inlineConverter = new MarkdownConverter($inlineEnvironment);
    }

    public function toHtml(?string $text): string
    {
        return $this->converter->convert($text);
    }

    public function toHtmlInline(?string $text): string
    {
        return $this->inlineConverter->convert($text);
    }
}
