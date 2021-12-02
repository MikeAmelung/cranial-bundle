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

    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'md',
                [$this, 'markdownToHtml'],
                [
                    'is_safe' => ['html'],
                ]
            ),
            new TwigFilter(
                'mdinline',
                [$this, 'markdownToHtmlInline'],
                [
                    'is_safe' => ['html'],
                ]
            ),
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

    public function getFunctions(): array
    {
        return [
            new TwigFunction('cranial_file', [
                CranialFileFunctionRuntime::class,
                'file',
            ]),
            new TwigFunction('cranial_image', [
                CranialImageFunctionRuntime::class,
                'image',
            ]),
        ];
    }
}
