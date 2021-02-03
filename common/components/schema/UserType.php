<?php

namespace common\components\schema;

use common\models\UserParams;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Class UserType
 * @package common\components\schema
 */
class UserType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => static function () {
                return [
                    'id' => [
                        'type' => Type::int(),
                    ],
                    'username' => [
                        'type' => Type::string(),
                    ],
                    'full_name' => [
                        'type' => Type::string(),
                    ],
                    'email' => [
                        'type' => Type::string(),
                    ],
                    'nickname' => [
                        'type' => Type::string(),
                    ],
                    'nickname_client_chat' => [
                        'type' => Type::string(),
                    ],
                    'status' => [
                        'type' => Type::int(),
                    ],
                    'acl_rules_activated' => [
                        'type' => Type::int(),
                    ],
                    'last_activity' => [
                        'type' => Type::string(),
                    ],
                    'created_at' => [
                        'type' => Type::string(),
                    ],
                    'updated_at' => [
                        'type' => Type::string(),
                    ],
                    'userParams' => [
                        'type' => Types::userParams(),
                        'resolve' => static function ($root, $args) {
                            $userId = \Yii::$app->user->id;
                            return UserParams::find()->where(['up_user_id' => $userId])->one();
                        }
                    ],
                ];
            }
        ];

        parent::__construct($config);
    }
}
