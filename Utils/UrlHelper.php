<?php

namespace MikeAmelung\CranialBundle\Utils;

class UrlHelper
{
    public static function urlEncode($prefix)
    {
        if (strpos($prefix, 'http://') === 0) {
            return 'http://' . rawurlencode(substr($prefix, 7));
        }

        if (strpos($prefix, 'https://') === 0) {
            return 'https://' . rawurlencode(substr($prefix, 8));
        }

        return rawurlencode($prefix);
    }
}
