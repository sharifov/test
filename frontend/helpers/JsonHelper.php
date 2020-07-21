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
        if (is_string($data)) {
            return $data;
        }
        if (!empty($data) && !is_string($data)) {
            return Json::htmlEncode($data);
        }
        return '';
    }

    /**
     * @param $data
     * @param bool $asArray
     * @return mixed|null
     */
    public static function decode($data, bool $asArray = true)
    {
        if (is_string($data)) {
            return Json::decode($data, $asArray);
        }
        return $data;
    }
}
