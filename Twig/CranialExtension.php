<?php

namespace MikeAmelung\CranialBundle\Twig;

use MikeAmelung\CranialBundle\Utils\Markdown;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

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
        ];
    }

    public function markdownToHtml($content)
    {
        return $this->parser->toHtml($content);
    }
}
