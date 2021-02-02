<?php

namespace MikeAmelung\CranialBundle\ImageProcessor;

use MikeAmelung\CranialBundle\Utils\UrlHelper;

use Aws\S3\S3Client;

class S3ImageProcessor implements ImageProcessorInterface
{
    private $imageDirectory;
    private $imageUrlPrefix;
    private $s3Bucket;
    private $s3Client;

    public function __construct($imageDirectory, $imageUrlPrefix, $s3Bucket, $s3Key, $s3Region, $s3Secret)
    {
        $this->imageDirectory = $imageDirectory;
        $this->imageUrlPrefix = $imageUrlPrefix;

        $this->s3Bucket = $s3Bucket;

        $this->s3Client = new S3Client([
            'region' => $s3Region,
            'version' => 'latest',
            'credentials' => [
                'key' => $s3Key,
                'secret' => $s3Secret,
            ],
        ]);
    }

    public function handleUpload($id, $image, $file)
    {
        if ($file) {
            if ($file->getError()) {
                throw new \Exception('There was a problem uploading the image.');
            }

            if (isset($image['filename']) && $image['filename']) {
            }

            $image['filename'] = $id . '.' . $file->guessExtension();

            $this->s3Client->putObject([
                'Bucket' => $this->s3Bucket,
                'Key' => $this->imageDirectory . '/' . $image['filename'],
                'SourceFile' => $file->getRealPath(),
                'ContentType' => $file->getMimeType(),
                'ACL' => 'public-read',
            ]);

            $image['path'] = UrlHelper::urlEncode($this->imageUrlPrefix . '/' . $image['filename']);
            $image['thumbnailPath'] = UrlHelper::urlEncode($this->imageUrlPrefix . '/thumbnails/' . $image['filename']);

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

    private function unlink($filename) {
        $path = $this->imageDirectory . '/' . $filename;
        $thumbnailPath = $this->imageDirectory .
            '/thumbnails/' .
            $filename;

        $this->s3Client->deleteObject([
            'Bucket' => $this->s3Bucket,
            'Key' => $path,
        ]);
        $this->s3Client->deleteObject([
            'Bucket' => $this->s3Bucket,
            'Key' => $thumbnailPath,
        ]);
    }

    public function generateThumbnail($filename)
    {
        $originalFilePath =
            $this->imageDirectory . '/' . $filename;
        $thumbnailFilePath =
            $this->imageDirectory .
            '/thumbnails/' .
            $filename;

        $thumb = new \Imagick();
        $thumb->readImageBlob($this->s3Client->getObject([
            'Bucket' => $this->s3Bucket,
            'Key' => $originalFilePath,
        ])['Body']);

        if ($thumb->getImageFormat() === 'GIF') {
            $thumb = $thumb->coalesceImages();
            do {
                $this->cropAndResize($thumb);
            } while ($thumb->nextImage());

            $thumb->deconstructImages();
        } else {
            $this->cropAndResize($thumb);
        }

        $this->s3Client->putObject([
            'Bucket' => $this->s3Bucket,
            'Key' => $thumbnailFilePath,
            'Body' => $thumb->getImageBlob(),
            'ContentType' => $thumb->getImageMimeType(),
        ]);

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
}
