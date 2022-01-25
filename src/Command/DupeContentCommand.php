<?php

namespace MikeAmelung\CranialBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use MikeAmelung\CranialBundle\ContentManager\ContentManager;

class DupeContentCommand extends Command
{
    protected static $defaultName = 'cranial:dupe-content';
    private $contentManager;

    public function __construct(ContentManager $contentManager)
    {
        parent::__construct();

        $this->contentManager = $contentManager;
    }

    protected function configure()
    {
        $this->setDescription('Duplicate a content item and get the new ID.')
            ->setHelp('...')
            ->addArgument(
                'contentId',
                InputArgument::REQUIRED,
                'The ID of the content to duplicate.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $existingContent = $this->contentManager->content(
            $input->getArgument('contentId')
        );

        if ($existingContent) {
            $newIdAndContent = $this->contentManager->createContent(
                $existingContent
            );

            $output->writeln('New content ID: ' . $newIdAndContent['id']);

            return Command::SUCCESS;
        }

        return Command::FAILURE;
    }
}
