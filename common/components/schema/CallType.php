<?php

namespace common\components\schema;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Class CallType
 * @package common\components\schema
 */
class CallType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => static function () {
                return [
                    'c_id' => [
                        'type' => Type::int(),
                    ],
                    'c_call_sid' => [
                        'type' => Type::string(),
                    ],
                    'c_call_type_id' => [
                        'type' => Type::int(),
                    ],
                    'c_from' => [
                        'type' => Type::string(),
                    ],
                    'c_to' => [
                        'type' => Type::string(),
                    ],
                    'c_parent_call_sid' => [
                        'type' => Type::string(),
                    ],
                    'c_lead_id' => [
                        'type' => Type::int(),
                    ],
                    'c_created_user_id' => [
                        'type' => Type::int(),
                    ],
                    'c_project_id' => [
                        'type' => Type::int(),
                    ],
                    'c_source_type_id' => [
                        'type' => Type::int(),
                    ],
                    'c_dep_id' => [
                        'type' => Type::int(),
                    ],
                    'c_case_id' => [
                        'type' => Type::int(),
                    ],
                    'c_client_id' => [
                        'type' => Type::int(),
                    ],
                    'c_status_id' => [
                        'type' => Type::int(),
                    ],
                    'c_parent_id' => [
                        'type' => Type::int(),
                    ],
                    'c_created_dt' => [
                        'type' => Type::string(),
                    ],
                    'c_updated_dt' => [
                        'type' => Type::string(),
                    ],
                ];
            }
        ];

        parent::__construct($config);
    }
}
