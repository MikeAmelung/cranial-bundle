<?php

namespace MikeAmelung\CranialBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MikeAmelungCranialBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
