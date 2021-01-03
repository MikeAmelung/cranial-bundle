<?php

namespace MikeAmelung\CranialBundle\Service;

class ImageProcessor
{
    private $imageDirectory;
    private $imagePathPrefix;

    public function __construct($imageDirectory, $imagePathPrefix)
    {
        $this->imageDirectory = $imageDirectory;
        $this->imagePathPrefix = $imagePathPrefix;
    }

    public function handleUpload($id, $image, $file)
    {
        if ($file) {
            if (!isset($image['filename'])) {
                $filename = $id . '.' . $file->guessExtension();
                $image['filename'] = $filename;
            }

            $file->move($this->imageDirectory, $image['filename']);

            $image['path'] = $this->imagePathPrefix . '/' . $image['filename'];
            $image['thumbnailPath'] = $this->imagePathPrefix . '/thumbnails/' . $image['filename'];

            $this->generateThumbnail($image['filename']);
        }

        return $image;
    }

    public function delete($image) {
        if (isset($file['filename'])) {
            $path = $this->imageDirectory . '/' . $image['filename'];

            if (file_exists($path)) {
                unlink($path);
            }

            $thumbnailPath = $this->imageDirectory .
                '/thumbnails/' .
                $image['filename'];

            if (file_exists($thumbnailPath)) {
                unlink($thumbnailPath);
            }
        }
    }

    public function generateThumbnail($filename)
    {
        $originalFilePath =
            $this->imageDirectory . '/' . $filename;
        $thumbnailFilePath =
            $this->imageDirectory .
            '/thumbnails/' .
            $filename;

        $thumb = new \Imagick($originalFilePath);

        if ($thumb->getImageFormat() === 'GIF') {
            $thumb = $thumb->coalesceImages();
            do {
                $this->cropAndResize($thumb);
            } while ($thumb->nextImage());

            $thumb->deconstructImages();
            $thumb->writeImages($thumbnailFilePath, true);
        } else {
            $this->cropAndResize($thumb);
            $thumb->writeImage($thumbnailFilePath);
        }

        $thumb->destroy();
    }

    private function cropAndResize(&$thumb)
    {
        $width = $thumb->getImageWidth();
        $height = $thumb->getImageHeight();

        if ($width === $height) {
        } elseif ($width > $height) {
            $trimStart = floor(($width - $height) / 2);
            $thumb->cropImage($height, $height, $trimStart, 0);
            $thumb->setImagePage($height, $height, 0, 0);
        } else {
            $trimStart = floor(($height - $width) / 2);
            $thumb->cropImage($width, $width, 0, $trimStart);
            $thumb->setImagePage($width, $width, 0, 0);
        }

        $thumb->resizeImage(200, 200, \Imagick::FILTER_LANCZOS, 1);
    }

    public function generateThumbnails()
    {
        foreach ($this->images as $id => $image) {
            $this->generateThumbnail($id);

            $imageThumbnailPath =
                $this->imagePathPrefix .
                '/thumbnails/' .
                $this->images[$id]['filename'];
            $this->images[$id]['thumbnailPath'] = $imageThumbnailPath;
        }

        file_put_contents(
            $this->contentDirectory . '/images.json',
            json_encode($this->images)
        );
    }
}
