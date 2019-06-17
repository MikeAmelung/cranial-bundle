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
    private $pages;

    private $twig;

    public function __construct($configDirectory, $contentDirectory, Environment $twig)
    {
        $this->configDirectory = $configDirectory;
        $this->types = json_decode(file_get_contents($configDirectory . '/content_types.json'), true);
        $this->templates = json_decode(file_get_contents($configDirectory . '/content_templates.json'), true);
        $this->pageTemplates = json_decode(file_get_contents($configDirectory . '/page_templates.json'), true);

        $this->contentDirectory = $contentDirectory;
        $this->content = json_decode(file_get_contents($contentDirectory . '/content.json'), true);
        $this->pages = json_decode(file_get_contents($contentDirectory . '/pages.json'), true);

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
            'value' => (new \DateTime())->format('m/d/Y H:i:s'),
        ];

        file_put_contents($this->contentDirectory . '/content.json', json_encode($this->content));

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
            'value' => (new \DateTime())->format('m/d/Y H:i:s'),
        ];

        file_put_contents($this->contentDirectory . '/content.json', json_encode($this->content));

        return $this->content[$id];
    }

    public function deleteContent($id)
    {
        if (isset($this->content[$id])) {
            unset($this->content[$id]);
        }

        file_put_contents($this->contentDirectory . '/content.json', json_encode($this->content));
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

        if (isset($this->content[$id]) && isset($this->content[$id]['templateKey'])) {
            $output .= $this->twig->render(
                'content/' . $this->content[$id]['templateKey'] . '.html.twig',
                array_merge(
                    [
                        'id' => $id,
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

    public function renderPageSlot($pageId, $slotKey, $container = ['tag' => 'div'])
    {
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
            foreach ($this->pages[$pageId]['contentMap'][$slotKey] as $contentId) {
                $output .= $this->renderContent($contentId);
            }
        }

        if ($container) {
            $output .= "</{$container['tag']}>";
        }

        return $output;
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
            'value' => (new \DateTime())->format('m/d/Y H:i:s'),
        ];

        file_put_contents($this->contentDirectory . '/pages.json', json_encode($this->pages));

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
            'value' => (new \DateTime())->format('m/d/Y H:i:s'),
        ];

        file_put_contents($this->contentDirectory . '/pages.json', json_encode($this->pages));

        return $this->pages[$id];
    }

    public function deletePage($id)
    {
        if (isset($this->pages[$id])) {
            unset($this->pages[$id]);
        }

        file_put_contents($this->contentDirectory . '/pages.json', json_encode($this->pages));
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
                        'templateId' => $pageTemplate['id'],
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
