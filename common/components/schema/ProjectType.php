<?php

namespace common\components\schema;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Class ProjectType
 * @package common\components\schema
 */
class ProjectType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => static function () {
                return [
                    'id' => [
                        'type' => Type::int(),
                    ],
                    'name' => [
                        'type' => Type::string(),
                    ],
                    'link' => [
                        'type' => Type::string(),
                    ],
                    'contact_info' => [
                        'type' => Type::string(),
                    ],
                    'project_key'  => [
                        'type' => Type::string(),
                    ],
                    'closed' => [
                        'type' => Type::boolean(),
                    ],
                    'last_update' => [
                        'type' => Type::string(),
                    ],
                    'sort_order' => [
                        'type' => Type::int(),
                    ],
                ];
            }
        ];

        parent::__construct($config);
    }
}
