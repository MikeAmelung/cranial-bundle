<?php

namespace MikeAmelung\CranialBundle\ImageProcessor;

interface ImageProcessorInterface
{
    public function handleUpload(string $id, $image, $file);

    public function delete($image);
}
