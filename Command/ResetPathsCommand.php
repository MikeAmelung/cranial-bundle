<?php

namespace MikeAmelung\CranialBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use MikeAmelung\CranialBundle\ContentManager\ContentManager;

class ResetPathsCommand extends Command
{
    protected static $defaultName = 'cranial:reset-paths';
    private $contentManager;

    public function __construct(ContentManager $contentManager)
    {
        parent::__construct();

        $this->contentManager = $contentManager;
    }

    protected function configure()
    {
        $this->setDescription(
            'Update images and files with new paths based on *UrlPrefix.'
        )
            ->setHelp('...')
            ->addArgument(
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

    protected function execute(InputInterface $input, OutputInterface $output)
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
