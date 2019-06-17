<?php

namespace MikeAmelung\CranialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use MikeAmelung\CranialBundle\Service\ContentManager;

/**
 * @Route("/cranial")
 */
class ContentController extends AbstractController
{
    /**
     * @Route("/content-types", name="mikeamelung_cranial_get_types", methods={"GET"})
     */
    public function contentTypes(ContentManager $contentManager, Request $request)
    {
        $types = $contentManager->getTypes();

        return new JsonResponse([
            'errors' => [],
            'types' => $types,
        ]);
    }

    /**
     * @Route("/content-templates", name="mikeamelung_cranial_get_templates", methods={"GET"})
     */
    public function contentTemplates(ContentManager $contentManager, Request $request)
    {
        $templates = $contentManager->getTemplates();

        return new JsonResponse([
            'errors' => [],
            'templates' => $templates,
        ]);
    }

    /**
     * @Route("/page-templates", name="mikeamelung_cranial_get_pages", methods={"GET"})
     */
    public function pageTemplates(ContentManager $contentManager, Request $request)
    {
        $pageTemplates = $contentManager->getPageTemplates();

        return new JsonResponse([
            'errors' => [],
            'pageTemplates' => $pageTemplates,
        ]);
    }

    /**
     * @Route("/all-content", name="mikeamelung_cranial_all_content", methods={"GET"})
     */
    public function allContent(ContentManager $contentManager, Request $request)
    {
        $content = $contentManager->allContent();

        return new JsonResponse([
            'errors' => [],
            'content' => $content,
        ]);
    }

    /**
     * @Route("/content/{id}", name="mikeamelung_cranial_get_content", methods={"GET"})
     */
    public function content(ContentManager $contentManager, Request $request, $id)
    {
        $content = $contentManager->content($id);

        return new JsonResponse([
            'errors' => [],
            'content' => $content,
        ]);
    }

    /**
     * @Route("/content/create", name="mikeamelung_cranial_create_content", methods={"POST"})
     */
    public function createContent(ContentManager $contentManager, Request $request)
    {
        $r = json_decode($request->getContent(), true);

        $contentAndId = $contentManager->createContent($r['content']);

        return new JsonResponse([
            'errors' => [],
            'id' => $contentAndId['id'],
            'content' => $contentAndId['content'],
        ]);
    }

    /**
     * @Route("/content/update", name="mikeamelung_cranial_update_content", methods={"POST"})
     */
    public function updateContent(ContentManager $contentManager, Request $request)
    {
        $r = json_decode($request->getContent(), true);

        $content = $contentManager->updateContent($r['id'], $r['content']);

        $rendered = $contentManager->renderContent($r['id']);

        return new JsonResponse([
            'errors' => [],
            'content' => $content,
            'rendered' => $rendered,
        ]);
    }

    /**
     * @Route("/content/{id}", name="mikeamelung_cranial_delete_content", methods={"DELETE"})
     */
    public function deleteContent(ContentManager $contentManager, Request $request, $id)
    {
        $contentManager->deleteContent($id);

        return new JsonResponse([
            'errors' => [],
        ]);
    }

    /**
     * @Route("/all-pages", name="mikeamelung_cranial_all_pages", methods={"GET"})
     */
    public function allPages(ContentManager $contentManager, Request $request)
    {
        $pages = $contentManager->allPages();

        return new JsonResponse([
            'errors' => [],
            'pages' => $pages,
        ]);
    }

    /**
     * @Route("/page/{id}", name="mikeamelung_cranial_get_page", methods={"GET"})
     */
    public function page(ContentManager $contentManager, Request $request, $id)
    {
        $page = $contentManager->page($id);

        return new JsonResponse([
            'errors' => [],
            'page' => $page,
        ]);
    }

    /**
     * @Route("/page/create", name="mikeamelung_cranial_create_page", methods={"POST"})
     */
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

    /**
     * @Route("/page/update", name="mikeamelung_cranial_update_page", methods={"POST"})
     */
    public function updatePage(ContentManager $contentManager, Request $request)
    {
        $r = json_decode($request->getContent(), true);

        $page = $contentManager->updatePage($r['id'], $r['page']);

        $renderedSlots = [];

        foreach ($r['page']['contentMap'] as $slotKey => $contentIds) {
            $renderedSlots[$slotKey] = $contentManager->renderPageSlot($r['id'], $slotKey, false);
        }

        return new JsonResponse([
            'errors' => [],
            'page' => $page,
            'renderedSlots' => $renderedSlots,
        ]);
    }

    /**
     * @Route("/page/{id}", name="mikeamelung_cranial_delete_page", methods={"DELETE"})
     */
    public function deletePage(ContentManager $contentManager, Request $request, $id)
    {
        $contentManager->deletePage($id);

        return new JsonResponse([
            'errors' => [],
        ]);
    }
}
