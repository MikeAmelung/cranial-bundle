<?php

namespace MikeAmelung\CranialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use MikeAmelung\CranialBundle\ContentManager\ContentManager;

class ContentController extends AbstractController
{
    #[Route('/cranial/content-types', name: 'mikeamelung_cranial_get_types', methods: ['GET'])]
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

    #[Route('/cranial/content-templates', name: 'mikeamelung_cranial_get_templates', methods: ['GET'])]
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

    #[Route('/cranial/page-templates', name: 'mikeamelung_cranial_get_pages', methods: ['GET'])]
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

    #[Route('/cranial/all-content', name: 'mikeamelung_cranial_all_content', methods: ['GET'])]
    public function allContent(ContentManager $contentManager, Request $request)
    {
        $content = $contentManager->allContent();

        return new JsonResponse([
            'errors' => [],
            'content' => $content,
        ]);
    }

    #[Route('/cranial/content/{id}', name: 'mikeamelung_cranial_content', methods: ['GET'])]
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

    #[Route('/cranial/content/create', name: 'mikeamelung_cranial_create_content', methods: ['POST'])]
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

    #[Route('/cranial/content/update', name: 'mikeamelung_cranial_update_content', methods: ['POST'])]
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

    #[Route('/cranial/content/{id}', name: 'mikeamelung_cranial_delete_content', methods: ['DELETE'])]
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

    #[Route('/cranial/all-files', name: 'mikeamelung_cranial_all_files', methods: ['GET'])]
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
    #[Route('/cranial/file/{id}', name: 'mikeamelung_cranial_file', methods: ['GET'])]
    public function getFile(ContentManager $contentManager, Request $request, $id)
    {
        $file = $contentManager->file($id);

        return new JsonResponse([
            'errors' => [],
            'file' => $file,
        ]);
    }

    #[Route('/cranial/file/create', name: 'mikeamelung_cranial_create_file', methods: ['POST'])]
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

    #[Route('/cranial/file/update', name: 'mikeamelung_cranial_update_file', methods: ['POST'])]
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

    #[Route('/cranial/file/{id}', name: 'mikeamelung_cranial_delete_file', methods: ['DELETE'])]
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

    #[Route('/cranial/all-images', name: 'mikeamelung_cranial_all_images', methods: ['GET'])]
    public function allImages(ContentManager $contentManager, Request $request)
    {
        $images = $contentManager->allImages();

        return new JsonResponse([
            'errors' => [],
            'images' => $images,
        ]);
    }

    #[Route('/cranial/image/{id}', name: 'mikeamelung_cranial_image', methods: ['GET'])]
    public function image(ContentManager $contentManager, Request $request, $id)
    {
        $image = $contentManager->image($id);

        return new JsonResponse([
            'errors' => [],
            'image' => $image,
        ]);
    }

    #[Route('/cranial/image/create', name: 'mikeamelung_cranial_create_image', methods: ['POST'])]
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

    #[Route('/cranial/image/update', name: 'mikeamelung_cranial_update_image', methods: ['POST'])]
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

    #[Route('/cranial/image/{id}', name: 'mikeamelung_cranial_delete_image', methods: ['DELETE'])]
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

    #[Route('/cranial/all-pages', name: 'mikeamelung_cranial_all_pages', methods: ['GET'])]
    public function allPages(ContentManager $contentManager, Request $request)
    {
        $pages = $contentManager->allPages();

        return new JsonResponse([
            'errors' => [],
            'pages' => $pages,
        ]);
    }

    #[Route('/cranial/page/{id}', name: 'mikeamelung_cranial_page', methods: ['GET'])]
    public function page(ContentManager $contentManager, Request $request, $id)
    {
        $page = $contentManager->page($id);

        return new JsonResponse([
            'errors' => [],
            'page' => $page,
        ]);
    }

    #[Route('/cranial/page/create', name: 'mikeamelung_cranial_create_page', methods: ['POST'])]
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

    #[Route('/cranial/page/update', name: 'mikeamelung_cranial_update_page', methods: ['POST'])]
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

    #[Route('/cranial/page/{id}', name: 'mikeamelung_cranial_delete_page', methods: ['DELETE'])]
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
