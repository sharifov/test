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
        if ($data && !is_string($data)) {
            return Json::htmlEncode($data);
        }
        return $data ?? '';
    }

    /**
     * @param $data
     * @param bool $asArray
     * @return mixed|null
     */
    public static function decode($data, bool $asArray = true)
    {
        if (is_string($data)) {
            return Json::decode($data);
        }
        return $data;
    }
}
