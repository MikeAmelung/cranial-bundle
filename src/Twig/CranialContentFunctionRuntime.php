<?php

namespace MikeAmelung\CranialBundle\Twig;

use Twig\Extension\RuntimeExtensionInterface;

use MikeAmelung\CranialBundle\ContentManager\ContentManager;

class CranialContentFunctionRuntime implements RuntimeExtensionInterface
{
    private $contentManager;

    public function __construct(ContentManager $contentManager)
    {
        $this->contentManager = $contentManager;
    }

    public function content($contentId, $container = ['tag' => 'div'], $templateOverride = '')
    {
        return $this->contentManager->renderContentWithDifferentTemplate($contentId, $container, $templateOverride);
    }
}
