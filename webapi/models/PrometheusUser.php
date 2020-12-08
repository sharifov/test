<?php

namespace webapi\models;

use Yii;
use yii\web\IdentityInterface;

class PrometheusUser implements IdentityInterface
{
    private static array $user = [
        'id' => 1001,
        'username' => 'PrometheusUser',
        //'password' => '',
        //'authKey' => '----',
        //'accessToken' => '1001-PrometheusUser-token',
    ];

    /**
     * @param int|string $id
     * @return IdentityInterface|static|null
     */
    public static function findIdentity($id)
    {
        return new static(self::$user);
    }

    /**
     * @param mixed $token
     * @param null $type
     * @return IdentityInterface|static|null
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        if (self::$user['accessToken'] === $token) {
            return new static(self::$user);
        }
        return null;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return ''; //$this->id;
    }

    /**
     * @return string
     */
    public function getAuthKey()
    {
        return ''; //$this->authKey;
    }

    /**
     * @param string $authKey
     * @return bool
     */
    public function validateAuthKey($authKey)
    {
        return true; //$this->authKey === $authKey;
    }

    /**
     * @param string $username
     * @param string $password
     * @return bool
     */
    public static function login(string $username = '', string $password = ''): bool
    {
        return (Yii::$app->prometheus->authUsername === $username && Yii::$app->prometheus->authPassword === $password);
    }
}
