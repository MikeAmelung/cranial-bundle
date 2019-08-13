<?php

namespace MikeAmelung\CranialBundle\Service;

use Ramsey\Uuid\Uuid;
use Twig\Environment;

class ContentManager
{
    private $configDirectory;
    private $types;
    private $templates;
    private $pageTemplates;

    private $contentDirectory;
    private $content;
    private $imageDirectory;
    private $imagePathPrefix;
    private $images;
    private $pages;

    private $twig;

    public function __construct(
        $configDirectory,
        $contentDirectory,
        $imageDirectory,
        $imagePathPrefix,
        Environment $twig
    ) {
        $this->configDirectory = $configDirectory;
        $this->types = json_decode(
            file_get_contents($configDirectory . '/content_types.json'),
            true
        );
        $this->templates = json_decode(
            file_get_contents($configDirectory . '/content_templates.json'),
            true
        );
        $this->pageTemplates = json_decode(
            file_get_contents($configDirectory . '/page_templates.json'),
            true
        );

        $this->contentDirectory = $contentDirectory;
        $this->content = json_decode(
            file_get_contents($contentDirectory . '/content.json'),
            true
        );

        $this->imageDirectory = $imageDirectory;
        $this->imagePathPrefix = $imagePathPrefix;
        $this->images = json_decode(
            file_get_contents($contentDirectory . '/images.json'),
            true
        );
        $this->pages = json_decode(
            file_get_contents($contentDirectory . '/pages.json'),
            true
        );

        $this->twig = $twig;
    }

    public function createContent($content)
    {
        $id = Uuid::uuid4()->toString();
        $this->content[$id] = $content;

        if (!isset($this->content[$id]['meta'])) {
            $this->content[$id]['meta'] = [];
        }
        $this->content[$id]['meta'][] = [
            'label' => 'Last Updated',
            'value' => (new \DateTime())->format('m/d/Y H:i:s')
        ];

        file_put_contents(
            $this->contentDirectory . '/content.json',
            json_encode($this->content)
        );

        return ['id' => $id, 'content' => $this->content[$id]];
    }

    public function updateContent($id, $content)
    {
        $this->content[$id] = $content;

        if (!isset($this->content[$id]['meta'])) {
            $this->content[$id]['meta'] = [];
        }
        $this->content[$id]['meta'][] = [
            'label' => 'Last Updated',
            'value' => (new \DateTime())->format('m/d/Y H:i:s')
        ];

        file_put_contents(
            $this->contentDirectory . '/content.json',
            json_encode($this->content)
        );

        return $this->content[$id];
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

    public function content($id)
    {
        if (isset($this->content[$id])) {
            return $this->content[$id];
        }
    }

    public function allContent()
    {
        return $this->content;
    }

    public function renderContent($id, $container = ['tag' => 'div'])
    {
        if ($container) {
            $attrs = "";

            if (isset($container['attr'])) {
                foreach ($container['attr'] as $attr => $val) {
                    $attrs .= "$attr=\"$val\"";
                }
            }

            $output = "<{$container['tag']} data-content-id=\"$id\" $attrs>";
        } else {
            $output = '';
        }

        if (
            isset($this->content[$id]) &&
            isset($this->content[$id]['templateKey'])
        ) {
            $output .= $this->twig->render(
                'content/' . $this->content[$id]['templateKey'] . '.html.twig',
                array_merge(
                    [
                        'id' => $id
                    ],
                    $this->content[$id]['data']
                )
            );
        }

        if ($container) {
            $output .= "</{$container['tag']}>";
        }

        if ($output) {
            return $output;
        }

        return "<div data-content-id=\"$id\"></div>";
    }

    public function renderPageSlot(
        $pageId,
        $slotKey,
        $container = ['tag' => 'div']
    ) {
        if ($container) {
            $attrs = "";

            if (isset($container['attr'])) {
                foreach ($container['attr'] as $attr => $val) {
                    $attrs .= "$attr=\"$val\"";
                }
            }

            $output = "<{$container['tag']} data-page-id=\"$pageId\" data-slot-key=\"$slotKey\" $attrs>";
        } else {
            $output = '';
        }

        if (isset($this->pages[$pageId]['contentMap'][$slotKey])) {
            foreach (
                $this->pages[$pageId]['contentMap'][$slotKey]
                as $contentId
            ) {
                $output .= $this->renderContent($contentId);
            }
        }

        if ($container) {
            $output .= "</{$container['tag']}>";
        }

        return $output;
    }

    public function createImage($image, $file)
    {
        $id = Uuid::uuid4()->toString();

        if ($file) {
            $filename = $id . '.' . $file->guessExtension();

            $file->move($this->imageDirectory, $filename);

            $image['filename'] = $filename;
            //TODO: allow config of image directory?
            $imagePath = $this->imagePathPrefix . '/' . $filename;
            $image['path'] = $imagePath;
            $imageThumbnailPath =
                $this->imagePathPrefix . '/thumbnails/' . $filename;
            $image['thumbnailPath'] = $imageThumbnailPath;
        }

        $this->images[$id] = $image;

        $this->generateThumbnail($id);

        if (!isset($this->images[$id]['meta'])) {
            $this->images[$id]['meta'] = [];
        }
        $this->images[$id]['meta'][] = [
            'label' => 'Last Updated',
            'value' => (new \DateTime())->format('m/d/Y H:i:s')
        ];

        file_put_contents(
            $this->contentDirectory . '/images.json',
            json_encode($this->images)
        );

        return ['id' => $id, 'image' => $this->images[$id]];
    }

    public function updateImage($id, $image, $file)
    {
        $this->images[$id] = $image;

        if ($file) {
            unlink(
                $this->imageDirectory . '/' . $this->images[$id]['filename']
            );
            $filename = $id . '.' . $file->guessExtension();

            $file->move($this->imageDirectory, $filename);

            $this->images[$id]['filename'] = $filename;
        }

        if (!isset($this->images[$id]['meta'])) {
            $this->images[$id]['meta'] = [];
        }
        $this->images[$id]['meta'][] = [
            'label' => 'Last Updated',
            'value' => (new \DateTime())->format('m/d/Y H:i:s')
        ];

        file_put_contents(
            $this->contentDirectory . '/images.json',
            json_encode($this->images)
        );

        $this->generateThumbnail($id);

        return $this->images[$id];
    }

    public function deleteImage($id)
    {
        if (isset($this->images[$id])) {
            unlink(
                $this->imageDirectory . '/' . $this->images[$id]['filename']
            );
            unlink(
                $this->imageDirectory .
                    '/thumbnails/' .
                    $this->images[$id]['filename']
            );
            unset($this->images[$id]);
        }

        file_put_contents(
            $this->contentDirectory . '/images.json',
            json_encode($this->images)
        );
    }

    public function image($id)
    {
        if (isset($this->images[$id])) {
            return $this->images[$id];
        }
    }

    public function allImages()
    {
        return $this->images;
    }

    public function generateThumbnail($id)
    {
        if (!isset($this->images[$id])) {
            return;
        }

        $filename =
            $this->imageDirectory . '/' . $this->images[$id]['filename'];
        $thumbnailFilename =
            $this->imageDirectory .
            '/thumbnails/' .
            $this->images[$id]['filename'];

        $thumb = new \Imagick($filename);

        if ($thumb->getImageFormat() === 'GIF') {
            $thumb = $thumb->coalesceImages();
            do {
                $this->cropAndResize($thumb);
            } while ($thumb->nextImage());

            $thumb->deconstructImages();
            $thumb->writeImages($thumbnailFilename, true);
        } else {
            $this->cropAndResize($thumb);
            $thumb->writeImage($thumbnailFilename);
        }

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

    public function generateThumbnails()
    {
        foreach ($this->images as $id => $image) {
            $this->generateThumbnail($id);

            $imageThumbnailPath =
                $this->imagePathPrefix .
                '/thumbnails/' .
                $this->images[$id]['filename'];
            $this->images[$id]['thumbnailPath'] = $imageThumbnailPath;
        }

        file_put_contents(
            $this->contentDirectory . '/images.json',
            json_encode($this->images)
        );
    }

    public function createPage($page)
    {
        $id = Uuid::uuid4()->toString();
        $this->pages[$id] = $page;

        if (!isset($this->pages[$id]['meta'])) {
            $this->pages[$id]['meta'] = [];
        }
        $this->pages[$id]['meta'][] = [
            'label' => 'Last Updated',
            'value' => (new \DateTime())->format('m/d/Y H:i:s')
        ];

        file_put_contents(
            $this->contentDirectory . '/pages.json',
            json_encode($this->pages)
        );

        return ['id' => $id, 'page' => $this->pages[$id]];
    }

    public function updatePage($id, $page)
    {
        if (isset($this->pages[$id])) {
            $this->pages[$id] = $page;
        }

        if (!isset($this->pages[$id]['meta'])) {
            $this->pages[$id]['meta'] = [];
        }
        $this->pages[$id]['meta'][] = [
            'label' => 'Last Updated',
            'value' => (new \DateTime())->format('m/d/Y H:i:s')
        ];

        file_put_contents(
            $this->contentDirectory . '/pages.json',
            json_encode($this->pages)
        );

        return $this->pages[$id];
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

    public function page($id)
    {
        return $this->pages[$id];
    }

    public function allPages()
    {
        return $this->pages;
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

    public function getTypes()
    {
        return $this->types;
    }

    public function getTemplates()
    {
        return $this->templates;
    }

    public function getPageTemplates()
    {
        return $this->pageTemplates;
    }
}
