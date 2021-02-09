<?php

namespace MikeAmelung\CranialBundle\Twig;

use Twig\Extension\RuntimeExtensionInterface;

use MikeAmelung\CranialBundle\ContentManager\ContentManager;

class CranialImageFunctionRuntime implements RuntimeExtensionInterface
{
    private $contentManager;

    public function __construct(ContentManager $contentManager)
    {
        $this->contentManager = $contentManager;
    }

    public function image($imageId)
    {
        return $this->contentManager->image($imageId);
    }
}
