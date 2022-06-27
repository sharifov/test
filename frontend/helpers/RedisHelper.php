<?php

namespace frontend\helpers;

use Yii;

class RedisHelper
{
    /**
     * @param string $idKey
     * @param int $pauseSecond
     * @return bool
     */
    public static function checkDuplicate(string $idKey, int $pauseSecond = 10, ?string $newValue = null): bool
    {
        if (empty($newValue)) {
            $newValue = Yii::$app->security->generateRandomString(6);
        }

        $redis = Yii::$app->redis;
        $isSetValue = $redis->setnx($idKey, $newValue);
        if ($isSetValue) {
            $redis->expire($idKey, $pauseSecond);
            return false;
        } else {
            return true;
        }
    }
}
