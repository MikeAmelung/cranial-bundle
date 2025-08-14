<?php

namespace MikeAmelung\CranialBundle\Utils;

use Embed\Embed;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Embed\Bridge\OscaroteroEmbedAdapter;
use League\CommonMark\Extension\Embed\Embed as EmbedNode;
use League\CommonMark\Extension\Embed\EmbedExtension;
use League\CommonMark\Extension\Embed\EmbedRenderer;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\InlinesOnly\InlinesOnlyExtension;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Renderer\HtmlDecorator;

class Markdown
{
    private $converter;
    private $inlineConverter;

    public function __construct()
    {
        $embedLibrary = new Embed();
        $embedLibrary->setSettings([
            'oembed:query_parameters' => [
                'maxwidth' => 1280,
                'maxheight' => 720,
            ],
        ]);

        $config = [
            'allow_unsafe_links' => false,
            'embed' => [
                'adapter' => new OscaroteroEmbedAdapter($embedLibrary),
                'allowed_domains' => ['youtube.com', 'vimeo.com'],
                'fallback' => 'link',
            ],
        ];

        $environment = new Environment($config);

        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->addExtension(new AttributesExtension());
        $environment->addExtension(new EmbedExtension());

        $environment->addRenderer(EmbedNode::class, new HtmlDecorator(new EmbedRenderer(), 'div', ['class' => 'video-container']));

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
