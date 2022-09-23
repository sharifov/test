<?php

namespace common\implementations\twoFactorAuth;

use kivork\TwoFactorAuth\Storage\StorageBaseInterface;

/**
 * Class AuthStorage
 * @package common\implementations\twoFactorAuth
 */
class AuthStorage implements StorageBaseInterface
{
    /** @var array $options */
    public $options = [];

    /**
     * @param $key
     * @param $value
     * @param $duration
     * @return mixed
     */
    public function set($key, $value, $duration)
    {
        $addingValue = serialize($value);
        $result = \Yii::$app->redis_2fa->set($key, $addingValue);
        \Yii::$app->redis_2fa->setex($key, $duration, $addingValue);
        return $result;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        $result = \Yii::$app->redis_2fa->get($key);
        return is_null($result) ? null : unserialize($result);
    }
}
