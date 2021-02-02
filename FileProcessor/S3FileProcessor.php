<?php

namespace MikeAmelung\CranialBundle\FileProcessor;

use Aws\S3\S3Client;

class S3FileProcessor implements FileProcessorInterface
{
    private $fileDirectory;
    private $fileUrlPrefix;
    private $s3Bucket;
    private $s3Client;

    public function __construct($fileDirectory, $fileUrlPrefix, $s3Bucket, $s3Key, $s3Region, $s3Secret)
    {
        $this->fileDirectory = $fileDirectory;
        $this->fileUrlPrefix = $fileUrlPrefix;

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

    public function handleUpload($id, $file, $uploadedFile)
    {
        if ($uploadedFile) {
            if ($uploadedFile->getError()) {
                throw new \Exception('There was a problem uploading the file.');
            }

            if (isset($file['filename']) && $file['filename']) {
                $this->unlink($file['filename']);
            }

            $file['filename'] = $uploadedFile->getClientOriginalName();

            $this->s3Client->putObject([
                'Bucket' => $this->s3Bucket,
                'Key' => $this->fileDirectory . '/' . $file['filename'],
                'SourceFile' => $uploadedFile->getRealPath(),
                'ContentType' => $uploadedFile->getMimeType(),
                'ACL' => 'public-read',
            ]);

            $file['path'] = $this->fileUrlPrefix . '/' . rawurlencode($file['filename']);
        }

        return $file;
    }

    public function delete($file)
    {
        if (isset($file['filename']) && $file['filename']) {
            $this->unlink($file['filename']);
        }
    }

    private function unlink($filename)
    {
        $this->s3Client->deleteObject([
            'Bucket' => $this->s3Bucket,
            'Key' => $this->fileDirectory . '/' . $filename,
        ]);
    }
}
