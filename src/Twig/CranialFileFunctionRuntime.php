<?php

namespace MikeAmelung\CranialBundle\Twig;

use Twig\Extension\RuntimeExtensionInterface;

use MikeAmelung\CranialBundle\ContentManager\ContentManager;

class CranialFileFunctionRuntime implements RuntimeExtensionInterface
{
    private $contentManager;

    public function __construct(ContentManager $contentManager)
    {
        $this->contentManager = $contentManager;
    }

    public function file($fileId)
    {
        return $this->contentManager->file($fileId);
    }
}
