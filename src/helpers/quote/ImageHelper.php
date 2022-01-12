<?php

namespace src\helpers\quote;

/**
 * Class ImageHelper
 */
class ImageHelper
{
    public static function checkImageGstaticExist(string $imageUrl): bool
    {
        if (strpos($imageUrl, 'http:') === false) {
            $imageUrl = 'http:' . $imageUrl;
        }

        try {
            return (bool) getimagesize($imageUrl);
        } catch (\Throwable $throwable) {
            return false;
        }
    }
}
