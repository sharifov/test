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
        if (!$redis->get($idKey)) {
            $redis->setex($idKey, $pauseSecond, true);
            return false;
        }
        return true;
    }
}
