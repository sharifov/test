<?php

namespace common\components\schema;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Class DepartmentType
 * @package common\components\schema
 */
class DepartmentType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => static function () {
                return [
                    'dep_id' => [
                        'type' => Type::int(),
                    ],
                    'dep_key' => [
                        'type' => Type::string(),
                    ],
                    'dep_name' => [
                        'type' => Type::string(),
                    ],
                    'dep_params' => [
                        'type' => Type::string(),
                    ],
                    'dep_updated_dt' => [
                        'type' => Type::string(),
                    ],
                    'dep_updated_user_id' => [
                        'type' => Type::int(),
                    ],
                ];
            }
        ];
        parent::__construct($config);
    }
}
