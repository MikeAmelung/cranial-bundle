<?php

namespace MikeAmelung\CranialBundle\FileProcessor;

use Aws\S3\S3Client;

class S3FileProcessor implements FileProcessorInterface
{
    private $fileUrlPrefix;
    private $s3Bucket;
    private $s3Client;

    public function __construct($fileUrlPrefix, $s3Bucket, $s3Key, $s3Region, $s3Secret)
    {
        $this->fileUrlPrefix = $fileUrlPrefix;

        $this->s3Bucket = $s3Bucket;

        $this->s3Client = new S3Client([
            'region' => $s3Region,
            'version' => '2006-03-01',
            'credentials' => [
                'key' => $s3Key,
                'secret' => $s3Secret,
            ],
        ]);
    }

    public function handleUpload($id, $file, $uploadedFile)
    {
        if ($uploadedFile) {
            if ($uploadedFile->getError()) {
                throw new \Exception('There was a problem uploading the file.');
            }

            if (isset($file['filename']) && $file['filename']) {
                $this->s3Client->deleteObject([
                    'Bucket' => $this->s3Bucket,
                    'Key' => $file['filename'],
                ]);
            }

            $file['filename'] = $uploadedFile->getClientOriginalName();

            $this->s3Client->putObject([
                'Bucket' => $this->s3Bucket,
                'Key' => $file['filename'],
                'SourceFile' => $uploadedFile->getRealPath(),
                'ContentType' => $uploadedFile->getMimeType(),
            ]);

            $file['path'] = $this->fileUrlPrefix . '/' . $file['filename'];
        }

        return $file;
    }

    public function delete($file)
    {
        if (isset($file['filename']) && $file['filename']) {
            $this->s3Client->deleteObject([
                'Bucket' => $this->s3Bucket,
                'Key' => $file['filename'],
            ]);
        }
    }
}
