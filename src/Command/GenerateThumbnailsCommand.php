<?php

namespace MikeAmelung\CranialBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use MikeAmelung\CranialBundle\ContentManager\ContentManager;

#[AsCommand(name: 'cranial:generate-thumbnails', description: 'Generate thumbnails for existing images')]
class GenerateThumbnailsCommand extends Command
{
    private $contentManager;

    public function __construct(ContentManager $contentManager)
    {
        $this->contentManager = $contentManager;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->contentManager->generateThumbnails();

        return Command::SUCCESS;
    }
}
