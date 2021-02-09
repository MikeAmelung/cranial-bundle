<?php

namespace MikeAmelung\CranialBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

use MikeAmelung\CranialBundle\Twig\CranialFileUrlFunctionRuntime;
use MikeAmelung\CranialBundle\Twig\CranialImageUrlFunctionRuntime;
use MikeAmelung\CranialBundle\Utils\Markdown;

class CranialExtension extends AbstractExtension
{
    private $parser;

    public function __construct(Markdown $parser)
    {
        $this->parser = $parser;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('md', [$this, 'markdownToHtml'], [
                'is_safe' => ['html'],
                'pre_escape' => 'html',
            ]),
            new TwigFilter('mdinline', [$this, 'markdownToHtmlInline'], [
                'is_safe' => ['html'],
                'pre_escape' => 'html',
            ]),
        ];
    }

    public function markdownToHtml($content)
    {
        return $this->parser->toHtml($content);
    }

    public function markdownToHtmlInline($content)
    {
        return $this->parser->toHtmlInline($content);
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('cranial_file_url', [CranialFileUrlFunctionRuntime::class, 'url']),
            new TwigFunction('cranial_image_url', [CranialImageUrlFunctionRuntime::class, 'url']),
        ];
    }
}
