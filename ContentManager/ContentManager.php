<?php

namespace MikeAmelung\CranialBundle\ContentManager;

use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Twig\Environment;

use MikeAmelung\CranialBundle\FileProcessor\FileProcessorInterface;
use MikeAmelung\CranialBundle\ImageProcessor\ImageProcessorInterface;
use MikeAmelung\CranialBundle\Storage\StorageInterface;

class ContentManager
{
    private $configDirectory;
    private $types;
    private $templates;
    private $pageTemplates;
    private $fileProcessor;
    private $imageProcessor;
    private $storage;
    private $twig;
    private $eventHandlers;

    private $skipEvents = false;

    public function __construct(
        $configDirectory,
        FileProcessorInterface $fileProcessor,
        ImageProcessorInterface $imageProcessor,
        StorageInterface $storage,
        Environment $twig,
        iterable $eventHandlers
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

        $this->fileProcessor = $fileProcessor;
        $this->imageProcessor = $imageProcessor;

        $this->storage = $storage;

        $this->twig = $twig;

        $this->eventHandlers = $eventHandlers;
    }

    public function allContent()
    {
        return $this->storage->allContent();
    }

    public function content($id)
    {
        return $this->storage->content($id);
    }

    public function contentByType($typeKey)
    {
        return $this->storage->contentByType($typeKey);
    }

    public function createContent($content)
    {
        $content = $this->createEvent('content', $content);

        $id = $this->storage->createContent($content);

        return ['id' => $id, 'content' => $content];
    }

    public function updateContent($id, $content)
    {
        $content = $this->updateEvent('content', $content);

        $this->storage->updateContent($id, $content);

        return $content;
    }

    public function deleteContent($id)
    {
        $this->storage->deleteContent($id);
    }

    public function allImages()
    {
        return $this->storage->allImages();
    }

    public function image($id)
    {
        return $this->storage->image($id);
    }

    public function imagePath($id)
    {
        $image = $this->storage->image($id);

        if ($image && isset($image['path'])) {
            return $image['path'];
        }

        return '';
    }

    public function createImage($image, $file)
    {
        $id = $this->storage->createImage($image);

        try {
            $processedImage = $this->imageProcessor->handleUpload(
                $id,
                $image,
                $file
            );
        } catch (\Exception $e) {
            $this->storage->deleteImage($id);

            throw $e;
        }

        $processedImage = $this->createEvent('image', $processedImage);

        $this->storage->updateImage($id, $processedImage);

        return ['id' => $id, 'image' => $processedImage];
    }

    public function updateImage($id, $image, $file)
    {
        try {
            $processedImage = $this->imageProcessor->handleUpload(
                $id,
                $image,
                $file
            );
        } catch (\Exception $e) {
            throw $e;
        }

        $processedImage = $this->updateEvent('image', $processedImage);

        $this->storage->updateImage($id, $processedImage);

        return $processedImage;
    }

    public function deleteImage($id)
    {
        $image = $this->storage->image($id);

        $this->imageProcessor->delete($image);

        $this->storage->deleteImage($id);
    }

    public function allFiles()
    {
        return $this->storage->allFiles();
    }

    public function file($id)
    {
        return $this->storage->file($id);
    }

    public function filePath($id)
    {
        $file = $this->storage->file($id);

        if ($file && isset($file['path'])) {
            return $file['path'];
        }

        return '';
    }

    public function createFile($file, $uploadedFile)
    {
        $id = $this->storage->createFile($file);

        try {
            $processedFile = $this->fileProcessor->handleUpload(
                $id,
                $file,
                $uploadedFile
            );
        } catch (\Exception $e) {
            $this->storage->deleteFile($id);

            throw $e;
        }

        $processedFile = $this->createEvent('file', $processedFile);

        $this->storage->updateFile($id, $processedFile);

        return ['id' => $id, 'file' => $processedFile];
    }

    public function updateFile($id, $file, $uploadedFile)
    {
        try {
            $processedFile = $this->fileProcessor->handleUpload(
                $id,
                $file,
                $uploadedFile
            );
        } catch (\Exception $e) {
            throw $e;
        }

        $processedFile = $this->updateEvent('file', $processedFile);

        $this->storage->updateFile($id, $processedFile);

        return $processedFile;
    }

    public function deleteFile($id)
    {
        $file = $this->storage->file($id);

        $this->fileProcessor->delete($file);

        $this->storage->deleteFile($id);
    }

    public function allPages()
    {
        return $this->storage->allPages();
    }

    public function page($id)
    {
        return $this->storage->page($id);
    }

    public function pageByRoute($route)
    {
        $pageIdAndPage = $this->storage->pageByRoute($route);

        if (!$pageIdAndPage) {
            return false;
        }

        return [
            'pageId' => $pageIdAndPage['pageId'],
            'templateId' =>
                $this->pageTemplates[$pageIdAndPage['page']['templateKey']][
                    'id'
                ],
        ];
    }

    public function createPage($page)
    {
        $page = $this->createEvent('page', $page);

        $id = $this->storage->createPage($page);

        return ['id' => $id, 'page' => $page];
    }

    public function updatePage($id, $page)
    {
        $page = $this->updateEvent('page', $page);

        $this->storage->updatePage($id, $page);

        return $page;
    }

    public function deletePage($id)
    {
        $this->storage->deletePage($id);
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

    public function renderContent($id, $container = ['tag' => 'div'])
    {
        if ($container) {
            $attrs = '';

            if (isset($container['attr'])) {
                foreach ($container['attr'] as $attr => $val) {
                    $attrs .= "$attr=\"$val\"";
                }
            }

            $output = "<{$container['tag']} data-content-id=\"$id\" $attrs>";
        } else {
            $output = '';
        }

        $content = $this->storage->content($id);

        /*
         * This is to allow using {% cranial_image_url(imageId) %} and {% cranial_file_url(fileId) %} functions
         * in the managed content.
         */
        $twiggedData = $content['data'];
        array_walk_recursive($twiggedData, function (&$data) {
            $dataTemplate = $this->twig->createTemplate($data);
            $data = $dataTemplate->render();
        });

        if ($content && isset($content['templateKey'])) {
            $output .= $this->twig->render(
                'content/' . $content['templateKey'] . '.html.twig',
                array_merge(
                    [
                        'id' => $id,
                    ],
                    $twiggedData
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
            $attrs = '';

            if (isset($container['attr'])) {
                foreach ($container['attr'] as $attr => $val) {
                    $attrs .= "$attr=\"$val\"";
                }
            }

            $output = "<{$container['tag']} data-page-id=\"$pageId\" data-slot-key=\"$slotKey\" $attrs>";
        } else {
            $output = '';
        }

        $page = $this->storage->page($pageId);

        if (isset($page['contentMap'][$slotKey])) {
            foreach ($page['contentMap'][$slotKey] as $contentId) {
                $output .= $this->renderContent($contentId);
            }
        }

        if ($container) {
            $output .= "</{$container['tag']}>";
        }

        return $output;
    }

    public function skipEvents()
    {
        $this->skipEvents = true;

        return $this;
    }

    private function createEvent($objectType, $object)
    {
        if ($this->skipEvents) {
            return $object;
        }

        foreach ($this->eventHandlers as $eventHandler) {
            $object = $eventHandler->handleCreate($objectType, $object);
        }

        return $object;
    }

    private function updateEvent($objectType, $object)
    {
        if ($this->skipEvents) {
            return $object;
        }

        foreach ($this->eventHandlers as $eventHandler) {
            $object = $eventHandler->handleUpdate($objectType, $object);
        }

        return $object;
    }
}
