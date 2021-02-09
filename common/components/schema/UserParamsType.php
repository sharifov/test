<?php

namespace common\components\schema;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Class UserParamsType
 * @package common\components\schema
 */
class UserParamsType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => static function () {
                return [
                    'up_user_id' => [
                        'type' => Type::int(),
                    ],
                    'up_timezone' => [
                        'type' => Type::string(),
                    ],
                ];
            }
        ];

        parent::__construct($config);
    }
}
