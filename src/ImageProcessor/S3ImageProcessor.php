<?php

namespace MikeAmelung\CranialBundle\ImageProcessor;

use Aws\S3\S3Client;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Vips\Driver as VipsDriver;

class S3ImageProcessor implements ImageProcessorInterface
{
    private $imageDirectory;
    private $imageUrlPrefix;
    private $s3Bucket;
    private $s3Client;

    public function __construct(
        $imageDirectory,
        $imageUrlPrefix,
        $s3Bucket,
        $s3Key,
        $s3Region,
        $s3Secret
    ) {
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
                throw new \Exception(
                    'There was a problem uploading the image.'
                );
            }

            if (isset($image['filename']) && $image['filename']) {
            }

            $image['filename'] = $id . '.' . $file->guessExtension();

            $this->s3Client->putObject([
                'Bucket' => $this->s3Bucket,
                'Key' => $this->imageDirectory . '/' . $image['filename'],
                'SourceFile' => $file->getRealPath(),
                'ContentType' => $file->getMimeType(),
            ]);

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
        $thumbnailPath = $this->imageDirectory . '/thumbnails/' . $filename;

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
        $originalFilePath = $this->imageDirectory . '/' . $filename;
        $thumbnailFilePath = $this->imageDirectory . '/thumbnails/' . $filename;

        $manager = new ImageManager(new VipsDriver());

        $imageBlob = $this->s3Client->getObject([
                'Bucket' => $this->s3Bucket,
                'Key' => $originalFilePath,
            ])['Body']
        ;

        $image = $manager->read($imageBlob);

        $image->cover(200, 200);

        $this->s3Client->putObject([
            'Bucket' => $this->s3Bucket,
            'Key' => $thumbnailFilePath,
            'Body' => (string) $image,
            'ContentType' => $image->mediaType(),
        ]);
    }
}
