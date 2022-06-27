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
    public static function checkDuplicate(string $idKey, int $pauseSecond = 10): bool
    {
        $redis = Yii::$app->redis;

        if ($redis->exists($idKey)) {
            return true;
        }

        $redis->set($idKey, true);
        $redis->expire($idKey, $pauseSecond);
        return false;
    }
}
