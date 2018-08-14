<?php

namespace webapi\models;

use yii\web\IdentityInterface;

class ApiUser implements IdentityInterface
{

    private static $users = [
        1 => [
            'id' => 1,
            'username' => 'apiuser',
            //'password' => '',
            //'authKey' => '----',
            //'accessToken' => '1-apiuser-token',
        ],
    ];

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }

        return null;
    }


    /**
     * @inheritdoc
     */
    public function getId()
    {
        return ''; //$this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return ''; //$this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return ''; //$this->authKey === $authKey;
    }


}