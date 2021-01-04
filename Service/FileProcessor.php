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

            if (isset($file['filename']) && $file['filename']) {
                $this->unlink($file['filename']);
            }

            $file['filename'] = $uploadedFile->getClientOriginalName();

            $uploadedFile->move($this->fileDirectory, $file['filename']);

            $file['path'] = $this->filePathPrefix . '/' . $file['filename'];
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
        $path = $this->fileDirectory . '/' . $filename;

        if (file_exists($path)) {
            unlink($path);
        }
    }
}
