<?php

namespace frontend\helpers;

use yii\helpers\Json;

/**
 * Class JsonHelper
 */
class JsonHelper
{
    /**
     * @param $data
     * @return string
     */
    public static function encode($data): string
    {
        if (!is_string($data)) {
            return Json::htmlEncode($data);
        }
        return $data;
    }

    /**
     * @param $data
     * @return mixed|null
     */
    public static function decode($data)
    {
        if (is_string($data)) {
            return Json::decode($data);
        }
        return $data;
    }
}
