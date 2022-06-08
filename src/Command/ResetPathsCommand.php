<?php

namespace MikeAmelung\CranialBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use MikeAmelung\CranialBundle\ContentManager\ContentManager;

#[AsCommand(name: 'cranial:reset-paths', description: 'Update images and files with new paths based on *UrlPrefix.')]
class ResetPathsCommand extends Command
{
    private $contentManager;

    public function __construct(ContentManager $contentManager)
    {
        $this->contentManager = $contentManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this ->addArgument(
                'type',
                InputArgument::REQUIRED,
                'Either image or file.'
            )
            ->addArgument(
                'prefix',
                InputArgument::REQUIRED,
                'The prefix for the path without trailing slash.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getArgument('type') === 'file') {
            $files = $this->contentManager->allFiles();

            foreach ($files as $id => $file) {
                $file['path'] =
                    $input->getArgument('prefix') .
                    '/' .
                    rawurlencode($file['filename']);
                $this->contentManager
                    ->skipEvents()
                    ->updateFile($id, $file, null);
            }

            return Command::SUCCESS;
        }

        if ($input->getArgument('type') === 'image') {
            $images = $this->contentManager->allImages();

            foreach ($images as $id => $image) {
                $image['path'] =
                    $input->getArgument('prefix') . '/' . $image['filename'];
                $image['thumbnailPath'] =
                    $input->getArgument('prefix') .
                    '/thumbnails/' .
                    $image['filename'];
                $this->contentManager
                    ->skipEvents()
                    ->updateImage($id, $image, null);
            }

            return Command::SUCCESS;
        }

        return Command::FAILURE;
    }
}
