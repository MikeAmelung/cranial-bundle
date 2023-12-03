<?php

namespace MikeAmelung\CranialBundle\Storage;

use MikeAmelung\CranialBundle\Entity\Content;
use MikeAmelung\CranialBundle\Entity\File;
use MikeAmelung\CranialBundle\Entity\Image;
use MikeAmelung\CranialBundle\Entity\Page;

class DatabaseStorage implements StorageInterface
{
    private $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function allContent()
    {
        $entities = $this->em->getRepository(Content::class)->findAll();

        $all = [];

        foreach ($entities as $entity) {
            $all[(string) $entity->getId()] = $entity->getPayload();
        }

        return $all;
    }

    public function content($id)
    {
        if (!$id) {
            return null;
        }

        $entity = $this->em->getRepository(Content::class)->find($id);

        if ($entity) {
            return $entity->getPayload();
        }
    }

    public function contentByType($typeKey)
    {
        $contentByType = [];

        $entities = $this->em
            ->getRepository(Content::class)
            ->createQueryBuilder('c')
            ->where("JSON_EXTRACT(c.payload, '$.typeKey') = :typeKey")
            ->setParameter('typeKey', $typeKey)
            ->getQuery()
            ->getResult();

        foreach ($entities as $entity) {
            $contentByType[
                $entity->getId()->__toString()
            ] = $entity->getPayload();
        }

        return $contentByType;
    }

    public function createContent($payload)
    {
        $entity = new Content();
        $entity->setPayload($payload);

        $this->em->persist($entity);
        $this->em->flush();

        return (string) $entity->getId();
    }

    public function updateContent($id, $payload)
    {
        $entity = $this->em->getRepository(Content::class)->find($id);
        $entity->setPayload($payload);

        $this->em->flush();
    }

    public function deleteContent($id)
    {
        $entity = $this->em->getRepository(Content::class)->find($id);

        $this->em->remove($entity);
        $this->em->flush();
    }

    public function allImages()
    {
        $entities = $this->em->getRepository(Image::class)->findAll();

        $all = [];

        foreach ($entities as $entity) {
            $all[(string) $entity->getId()] = $entity->getPayload();
        }

        return $all;
    }

    public function image($id)
    {
        if (!$id) {
            return null;
        }

        $entity = $this->em->getRepository(Image::class)->find($id);

        if ($entity) {
            return $entity->getPayload();
        }
    }

    public function createImage($payload)
    {
        $entity = new Image();
        $entity->setPayload($payload);

        $this->em->persist($entity);
        $this->em->flush();

        return (string) $entity->getId();
    }

    public function updateImage($id, $payload)
    {
        $entity = $this->em->getRepository(Image::class)->find($id);
        $entity->setPayload($payload);

        $this->em->flush();
    }

    public function deleteImage($id)
    {
        $entity = $this->em->getRepository(Image::class)->find($id);

        $this->em->remove($entity);
        $this->em->flush();
    }

    public function allFiles()
    {
        $entities = $this->em->getRepository(File::class)->findAll();

        $all = [];

        foreach ($entities as $entity) {
            $all[(string) $entity->getId()] = $entity->getPayload();
        }

        return $all;
    }

    public function file($id)
    {
        if (!$id) {
            return null;
        }

        $entity = $this->em->getRepository(File::class)->find($id);

        if ($entity) {
            return $entity->getPayload();
        }
    }

    public function createFile($payload)
    {
        $entity = new File();
        $entity->setPayload($payload);

        $this->em->persist($entity);
        $this->em->flush();

        return (string) $entity->getId();
    }

    public function updateFile($id, $payload)
    {
        $entity = $this->em->getRepository(File::class)->find($id);
        $entity->setPayload($payload);

        $this->em->flush();
    }

    public function deleteFile($id)
    {
        $entity = $this->em->getRepository(File::class)->find($id);

        $this->em->remove($entity);
        $this->em->flush();
    }

    public function allPages()
    {
        $entities = $this->em->getRepository(Page::class)->findAll();

        $all = [];

        foreach ($entities as $entity) {
            $all[(string) $entity->getId()] = $entity->getPayload();
        }

        return $all;
    }

    public function page($id)
    {
        if (!$id) {
            return null;
        }

        $entity = $this->em->getRepository(Page::class)->find($id);

        if ($entity) {
            return $entity->getPayload();
        }
    }

    public function pageByRoute($route)
    {
        $entity = $this->em
            ->getRepository(Page::class)
            ->createQueryBuilder('p')
            ->where("JSON_EXTRACT(p.payload, '$.route') = :route")
            ->setParameter('route', $route)
            ->getQuery()
            ->getOneOrNullResult();

        if ($entity) {
            return [
                'pageId' => $entity->getId(),
                'page' => $entity->getPayload(),
            ];
        }

        return false;
    }

    public function createPage($payload)
    {
        $entity = new Page();
        $entity->setPayload($payload);

        $this->em->persist($entity);
        $this->em->flush();

        return (string) $entity->getId();
    }

    public function updatePage($id, $payload)
    {
        $entity = $this->em->getRepository(Page::class)->find($id);
        $entity->setPayload($payload);

        $this->em->flush();
    }

    public function deletePage($id)
    {
        $entity = $this->em->getRepository(Page::class)->find($id);

        $this->em->remove($entity);
        $this->em->flush();
    }
}
