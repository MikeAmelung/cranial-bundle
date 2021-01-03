<?php

namespace MikeAmelung\CranialBundle\Service;

class FileProcessor
{
    private $fileDirectory;
    private $filePathPrefix;

    public function __construct($fileDirectory, $filePathPrefix)
    {
        $this->fileDirectory = $fileDirectory;
        $this->filePathPrefix = $filePathPrefix;
    }

    public function handleUpload($id, $file, $uploadedFile)
    {
        if ($uploadedFile) {
            if ($uploadedFile->getError()) {
                throw new \Exception('There was a problem uploading the file.');
            }

            if (!isset($file['filename'])) {
                $file['filename'] = $id . '.' . $uploadedFile->guessExtension();
            }

            $uploadedFile->move($this->fileDirectory, $file['filename']);

            $file['path'] = $this->filePathPrefix . '/' . $file['filename'];
        }

        return $file;
    }

    public function delete($file) {
        if (isset($file['filename'])) {
            unlink(
                $this->fileDirectory . '/' . $file['filename']
            );
        }
    }
}
