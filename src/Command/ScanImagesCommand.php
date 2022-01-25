<?php

namespace MikeAmelung\CranialBundle\Command;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

use MikeAmelung\CranialBundle\ContentManager\ContentManager;
use MikeAmelung\CranialBundle\ImageProcessor\ImageProcessorInterface;

class ScanImagesCommand extends Command
{
    private $contentManager;
    private $imageProcessor;

    public function __construct(
        ContentManager $contentManager,
        ImageProcessorInterface $imageProcessor
    ) {
        $this->contentManager = $contentManager;
        $this->imageProcessor = $imageProcessor;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('cranial:scan-images')->setDescription(
            'Scan images to pre-populate the image content repository'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /*
        TODO: Move this into each image processor

        $finder = new Finder();

        $files = $finder->files()->in($this->imageDirectory);

        foreach ($files as $file) {
            $this->contentManager->createImage([], $file);
        }
        */

        return Command::SUCCESS;
    }
}
