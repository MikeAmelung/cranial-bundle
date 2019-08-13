<?php

namespace MikeAmelung\CranialBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use MikeAmelung\CranialBundle\Service\ContentManager;

class GenerateThumbnailsCommand extends Command
{
    private $contentManager;

    public function __construct(ContentManager $contentManager)
    {
        $this->contentManager = $contentManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('cranial:generate-thumbnails')->setDescription(
            'Generate thumbnails for existing images'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->contentManager->generateThumbnails();
    }
}
