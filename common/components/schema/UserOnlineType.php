<?php

namespace common\components\schema;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Class UserOnline
 * @package common\components\schema
 */
class UserOnlineType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => static function () {
                return [
                    'uo_user_id' => [
                        'type' => Type::int(),
                    ],
                    'uo_idle_state' => [
                        'type' => Type::boolean(),
                    ],
                    'uo_updated_dt' => [
                        'type' => Type::string(),
                    ],
                    'uo_idle_state_dt' => [
                        'type' => Type::string(),
                    ],
                ];
            }
        ];

        parent::__construct($config);
    }
}
