<?php

namespace MikeAmelung\CranialBundle\Twig;

use Twig\Extension\RuntimeExtensionInterface;

use MikeAmelung\CranialBundle\ContentManager\ContentManager;

class CranialFileUrlFunctionRuntime implements RuntimeExtensionInterface
{
    private $contentManager;

    public function __construct(ContentManager $contentManager)
    {
        $this->contentManager = $contentManager;
    }

    public function url($fileId)
    {
        return $this->contentManager->filePath($fileId);
    }
}
