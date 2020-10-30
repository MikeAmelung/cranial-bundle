<?php

namespace MikeAmelung\CranialBundle\Storage;

interface StorageInterface
{
    public function allContent();

    public function content(string $id);

    public function contentByType(string $typeKey);

    public function createContent($content);

    public function updateContent(string $id, $content);

    public function deleteContent(string $id);

    public function allImages();

    public function image(string $id);

    public function createImage($image);

    public function updateImage(string $id, $image);

    public function deleteImage(string $id);

    public function allPages();

    public function page(string $id);

    public function pageByRoute(string $route);

    public function createPage($page);

    public function updatePage(string $id, $page);

    public function deletePage(string $id);
}
