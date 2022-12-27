<?php

namespace MikeAmelung\CranialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use MikeAmelung\CranialBundle\ContentManager\ContentManager;

class ContentController extends AbstractController
{
    public function contentTypes(
        ContentManager $contentManager,
        Request $request
    ) {
        $types = $contentManager->getTypes();

        return new JsonResponse([
            'errors' => [],
            'types' => $types,
        ]);
    }

    public function contentTemplates(
        ContentManager $contentManager,
        Request $request
    ) {
        $templates = $contentManager->getTemplates();

        return new JsonResponse([
            'errors' => [],
            'templates' => $templates,
        ]);
    }

    public function pageTemplates(
        ContentManager $contentManager,
        Request $request
    ) {
        $pageTemplates = $contentManager->getPageTemplates();

        return new JsonResponse([
            'errors' => [],
            'pageTemplates' => $pageTemplates,
        ]);
    }

    public function allContent(ContentManager $contentManager, Request $request)
    {
        $content = $contentManager->allContent();

        return new JsonResponse([
            'errors' => [],
            'content' => $content,
        ]);
    }

    public function content(
        ContentManager $contentManager,
        Request $request,
        $id
    ) {
        $content = $contentManager->content($id);

        return new JsonResponse([
            'errors' => [],
            'content' => $content,
        ]);
    }

    public function createContent(
        ContentManager $contentManager,
        Request $request
    ) {
        $r = json_decode($request->getContent(), true);

        $contentAndId = $contentManager->createContent($r['content']);

        return new JsonResponse([
            'errors' => [],
            'id' => $contentAndId['id'],
            'content' => $contentAndId['content'],
        ]);
    }

    public function updateContent(
        ContentManager $contentManager,
        Request $request
    ) {
        $r = json_decode($request->getContent(), true);

        $content = $contentManager->updateContent($r['id'], $r['content']);

        $rendered = $contentManager->renderContent($r['id'], false);

        return new JsonResponse([
            'errors' => [],
            'content' => $content,
            'rendered' => $rendered,
        ]);
    }

    public function deleteContent(
        ContentManager $contentManager,
        Request $request,
        $id
    ) {
        $contentManager->deleteContent($id);

        return new JsonResponse([
            'errors' => [],
        ]);
    }

    public function allFiles(ContentManager $contentManager, Request $request)
    {
        $files = $contentManager->allFiles();

        return new JsonResponse([
            'errors' => [],
            'files' => $files,
        ]);
    }

    /**
     * Function name is getFile to avoid collision with AbstractController::file
     */
    public function getFile(ContentManager $contentManager, Request $request, $id)
    {
        $file = $contentManager->file($id);

        return new JsonResponse([
            'errors' => [],
            'file' => $file,
        ]);
    }

    public function createFile(ContentManager $contentManager, Request $request)
    {
        $r = json_decode($request->request->get('json'), true);
        $uploadedFile = $request->files->get('file');

        try {
            $fileAndId = $contentManager->createFile($r['file'], $uploadedFile);
        } catch (\Exception $e) {
            return new JsonResponse([
                'errors' => [$e->getMessage()],
                'id' => null,
                'file' => null,
            ]);
        }

        return new JsonResponse([
            'errors' => [],
            'id' => $fileAndId['id'],
            'file' => $fileAndId['file'],
        ]);
    }

    public function updateFile(ContentManager $contentManager, Request $request)
    {
        $r = json_decode($request->request->get('json'), true);
        $uploadedFile = $request->files->get('file');

        $file = $contentManager->updateFile(
            $r['id'],
            $r['file'],
            $uploadedFile
        );

        return new JsonResponse([
            'errors' => [],
            'file' => $file,
        ]);
    }

    public function deleteFile(
        ContentManager $contentManager,
        Request $request,
        $id
    ) {
        $contentManager->deleteFile($id);

        return new JsonResponse([
            'errors' => [],
        ]);
    }

    public function allImages(ContentManager $contentManager, Request $request)
    {
        $images = $contentManager->allImages();

        return new JsonResponse([
            'errors' => [],
            'images' => $images,
        ]);
    }

    public function image(ContentManager $contentManager, Request $request, $id)
    {
        $image = $contentManager->image($id);

        return new JsonResponse([
            'errors' => [],
            'image' => $image,
        ]);
    }

    public function createImage(
        ContentManager $contentManager,
        Request $request
    ) {
        $r = json_decode($request->request->get('json'), true);
        $file = $request->files->get('file');

        $imageAndId = $contentManager->createImage($r['image'], $file);

        return new JsonResponse([
            'errors' => [],
            'id' => $imageAndId['id'],
            'image' => $imageAndId['image'],
        ]);
    }

    public function updateImage(
        ContentManager $contentManager,
        Request $request
    ) {
        $r = json_decode($request->request->get('json'), true);
        $file = $request->files->get('file');

        $image = $contentManager->updateImage($r['id'], $r['image'], $file);

        return new JsonResponse([
            'errors' => [],
            'image' => $image,
        ]);
    }

    public function deleteImage(
        ContentManager $contentManager,
        Request $request,
        $id
    ) {
        $contentManager->deleteImage($id);

        return new JsonResponse([
            'errors' => [],
        ]);
    }

    public function allPages(ContentManager $contentManager, Request $request)
    {
        $pages = $contentManager->allPages();

        return new JsonResponse([
            'errors' => [],
            'pages' => $pages,
        ]);
    }

    public function page(ContentManager $contentManager, Request $request, $id)
    {
        $page = $contentManager->page($id);

        return new JsonResponse([
            'errors' => [],
            'page' => $page,
        ]);
    }

    public function createPage(ContentManager $contentManager, Request $request)
    {
        $r = json_decode($request->getContent(), true);

        $pageAndId = $contentManager->createPage($r['page']);

        return new JsonResponse([
            'errors' => [],
            'id' => $pageAndId['id'],
            'page' => $pageAndId['page'],
        ]);
    }

    public function updatePage(ContentManager $contentManager, Request $request)
    {
        $r = json_decode($request->getContent(), true);

        $page = $contentManager->updatePage($r['id'], $r['page']);

        $renderedSlots = [];

        foreach ($r['page']['contentMap'] as $slotKey => $contentIds) {
            $renderedSlots[$slotKey] = $contentManager->renderPageSlot(
                $r['id'],
                $slotKey,
                false
            );
        }

        return new JsonResponse([
            'errors' => [],
            'page' => $page,
            'renderedSlots' => $renderedSlots,
        ]);
    }

    public function deletePage(
        ContentManager $contentManager,
        Request $request,
        $id
    ) {
        $contentManager->deletePage($id);

        return new JsonResponse([
            'errors' => [],
        ]);
    }
}
