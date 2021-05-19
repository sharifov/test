<?php

namespace sales\helpers\quote;

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
            \Yii::error(
                \yii\helpers\VarDumper::dumpAsString($throwable, 10, true),
                'Debug:' . self::class . ':' . __FUNCTION__
            );
            /* TODO: to remove */
            return false;
        }
    }
}
