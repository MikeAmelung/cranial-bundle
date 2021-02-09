<?php

namespace MikeAmelung\CranialBundle\Twig;

use Twig\Extension\RuntimeExtensionInterface;

use MikeAmelung\CranialBundle\ContentManager\ContentManager;

class CranialImageUrlFunctionRuntime implements RuntimeExtensionInterface
{
    private $contentManager;

    public function __construct(ContentManager $contentManager)
    {
        $this->contentManager = $contentManager;
    }

    public function url($imageId)
    {
        return $this->contentManager->imagePath($imageId);
    }
}
