<?php

namespace MikeAmelung\CranialBundle\EventHandlers;

class LastUpdated
{
    public function handleCreate(string $objectType, $object)
    {
        if (!isset($object['meta'])) {
            $object['meta'] = [];
        }

        $object['meta']['last_updated'] = [
            'label' => 'Last Updated',
            'value' => (new \DateTime())->format('m/d/Y H:i:s'),
        ];

        return $object;
    }

    public function handleUpdate(string $objectType, $object)
    {
        if (!isset($object['meta'])) {
            $object['meta'] = [];
        }

        $object['meta']['last_updated'] = [
            'label' => 'Last Updated',
            'value' => (new \DateTime())->format('m/d/Y H:i:s'),
        ];

        return $object;
    }
}
