<?php

namespace common\components\schema;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Class ArrayType
 * @package common\components\schema
 */
class ArrayType extends ObjectType
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
                ];
            }
        ];
        parent::__construct($config);
    }
}
