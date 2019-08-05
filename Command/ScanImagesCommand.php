<?php

namespace MikeAmelung\CranialBundle\Command;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class ScanImagesCommand extends Command
{
    private $contentDirectory;
    private $imageDirectory;

    public function __construct(
        string $contentDirectory,
        string $imageDirectory
    ) {
        $this->contentDirectory = $contentDirectory;
        $this->imageDirectory = $imageDirectory;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('cranial:scan-images')->setDescription(
            'Scan images to pre-populate the image content repository'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new Finder();

        $files = $finder->files()->in($this->imageDirectory);

        $images = [];

        foreach ($files as $file) {
            $images[Uuid::uuid4()->toString()] = [
                'filename' => $file->getFilename()
            ];
        }

        file_put_contents(
            $this->contentDirectory . '/images.json',
            json_encode($images)
        );
    }
}
