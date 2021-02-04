<?php

namespace common\components\schema;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Class UserStatusType
 * @package common\components\schema
 */
class UserStatusType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => static function () {
                return [
                    'us_user_id' => [
                        'type' => Type::int(),
                    ],
                    'us_gl_call_count' => [
                        'type' => Type::int(),
                    ],
                    'us_call_phone_status' => [
                        'type' => Type::boolean(),
                    ],
                    'us_is_on_call' => [
                        'type' => Type::boolean(),
                    ],
                    'us_has_call_access' => [
                        'type' => Type::boolean(),
                    ],
                    'us_updated_dt' => [
                        'type' => Type::string(),
                    ],
                ];
            }
        ];

        parent::__construct($config);
    }
}
