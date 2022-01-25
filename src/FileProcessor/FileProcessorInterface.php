<?php

namespace MikeAmelung\CranialBundle\FileProcessor;

interface FileProcessorInterface
{
    public function handleUpload(string $id, $file, $uploadedFile);

    public function delete($file);
}
