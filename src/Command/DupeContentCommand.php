<?php

namespace MikeAmelung\CranialBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use MikeAmelung\CranialBundle\ContentManager\ContentManager;

#[AsCommand(name: 'cranial:dupe-content', description: 'Duplicate a content item and get the new ID.')]
class DupeContentCommand extends Command
{
    private $contentManager;

    public function __construct(ContentManager $contentManager)
    {
        $this->contentManager = $contentManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument(
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
