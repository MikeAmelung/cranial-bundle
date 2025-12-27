<?php

namespace MikeAmelung\CranialBundle\ImageProcessor;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Vips\Driver as VipsDriver;

class LocalImageProcessor implements ImageProcessorInterface
{
    private $imageDirectory;
    private $imageUrlPrefix;

    public function __construct($imageDirectory, $imageUrlPrefix)
    {
        $this->imageDirectory = $imageDirectory;
        $this->imageUrlPrefix = $imageUrlPrefix;
    }

    public function handleUpload($id, $image, $file)
    {
        if ($file) {
            if ($file->getError()) {
                throw new \Exception(
                    'There was a problem uploading the image.'
                );
            }

            if (isset($image['filename']) && $image['filename']) {
                $this->unlink($image['filename']);
            }

            $image['filename'] = $id . '.' . $file->guessExtension();

            $file->move($this->imageDirectory, $image['filename']);

            $image['path'] = $this->imageUrlPrefix . '/' . $image['filename'];
            $image['thumbnailPath'] =
                $this->imageUrlPrefix . '/thumbnails/' . $image['filename'];

            $this->generateThumbnail($image['filename']);
        }

        return $image;
    }

    public function delete($image)
    {
        if (isset($image['filename']) && $image['filename']) {
            $this->unlink($image['filename']);
        }
    }

    private function unlink($filename)
    {
        $path = $this->imageDirectory . '/' . $filename;

        if (file_exists($path)) {
            unlink($path);
        }

        $thumbnailPath = $this->imageDirectory . '/thumbnails/' . $filename;

        if (file_exists($thumbnailPath)) {
            unlink($thumbnailPath);
        }
    }

    public function generateThumbnail($filename)
    {
        $originalFilePath = $this->imageDirectory . '/' . $filename;
        $thumbnailFilePath = $this->imageDirectory . '/thumbnails/' . $filename;

        $manager = new ImageManager(new VipsDriver());

        $image = $manager->read($originalFilePath);

        $image->cover(200, 200);

        $image->save($thumbnailFilePath);
    }
}
