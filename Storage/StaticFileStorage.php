<?php

use Ramsey\Uuid\Uuid;

namespace MikeAmelung\CranialBundle\Storage;

class StaticFileStorage implements StorageInterface
{
    private $contentDirectory;
    private $content;
    private $pages;
    private $images;

    public function __construct($contentDirectory)
    {
        $this->contentDirectory = $contentDirectory;

        $this->content = json_decode(
            file_get_contents($contentDirectory . '/content.json'),
            true
        );

        $this->images = json_decode(
            file_get_contents($contentDirectory . '/images.json'),
            true
        );

        $this->pages = json_decode(
            file_get_contents($contentDirectory . '/pages.json'),
            true
        );
    }

    public function allContent()
    {
        return $this->content;
    }

    public function content($id)
    {
        if (isset($this->content[$id])) {
            return $this->content[$id];
        }
    }

    public function createContent($content)
    {
        $id = Uuid::uuid4()->toString();

        $this->content[$id] = $content;

        file_put_contents(
            $this->contentDirectory . '/content.json',
            json_encode($this->content)
        );

        return $id;
    }

    public function updateContent($id, $content)
    {
        $this->content[$id] = $content;

        file_put_contents(
            $this->contentDirectory . '/content.json',
            json_encode($this->content)
        );
    }

    public function deleteContent($id)
    {
        if (isset($this->content[$id])) {
            unset($this->content[$id]);
        }

        file_put_contents(
            $this->contentDirectory . '/content.json',
            json_encode($this->content)
        );
    }

    public function allImages()
    {
        return $this->images;
    }

    public function image($id)
    {
        if (isset($this->images[$id])) {
            return $this->images[$id];
        }
    }

    public function createImage($image)
    {
        $id = Uuid::uuid4()->toString();

        $this->images[$id] = $image;

        file_put_contents(
            $this->contentDirectory . '/images.json',
            json_encode($this->images)
        );

        return $id;
    }

    public function updateImage($id, $image)
    {
        $this->images[$id] = $image;

        file_put_contents(
            $this->contentDirectory . '/images.json',
            json_encode($this->images)
        );
    }

    public function deleteImage($id)
    {
        if (isset($this->images[$id])) {
            unset($this->images[$id]);
        }

        file_put_contents(
            $this->contentDirectory . '/images.json',
            json_encode($this->images)
        );
    }

    public function allPages()
    {
        return $this->pages;
    }

    public function page($id)
    {
        return $this->pages[$id];
    }

    public function pageByRoute($route)
    {
        if ($this->pages) {
            foreach ($this->pages as $pageId => $page) {
                if ($page['route'] === $route) {
                    $pageTemplate = $this->pageTemplates[$page['templateKey']];

                    return [
                        'pageId' => $pageId,
                        'templateId' => $pageTemplate['id']
                    ];
                }
            }
        }

        return false;
    }

    public function createPage($page)
    {
        $id = Uuid::uuid4()->toString();

        $this->pages[$id] = $page;

        file_put_contents(
            $this->contentDirectory . '/pages.json',
            json_encode($this->pages)
        );

        return $id;
    }

    public function updatePage($id, $page)
    {
        $this->pages[$id] = $page;

        file_put_contents(
            $this->contentDirectory . '/pages.json',
            json_encode($this->pages)
        );
    }

    public function deletePage($id)
    {
        if (isset($this->pages[$id])) {
            unset($this->pages[$id]);
        }

        file_put_contents(
            $this->contentDirectory . '/pages.json',
            json_encode($this->pages)
        );
    }
}
