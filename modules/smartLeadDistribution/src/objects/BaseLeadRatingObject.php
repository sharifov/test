<?php

namespace modules\smartLeadDistribution\src\objects;

use src\access\ConditionExpressionInterface;

class BaseLeadRatingObject implements ConditionExpressionInterface
{
    protected const ATTR_TYPE_INTEGER  = 'integer';
    protected const ATTR_TYPE_STRING   = 'string';
    protected const ATTR_TYPE_DOUBLE   = 'double';
    protected const ATTR_TYPE_DATE   = 'date';
    protected const ATTR_TYPE_TIME   = 'time';
    protected const ATTR_TYPE_DATETIME   = 'datetime';
    protected const ATTR_TYPE_BOOLEAN   = 'boolean';

    protected const ATTR_INPUT_RADIO       = 'radio';
    protected const ATTR_INPUT_CHECKBOX    = 'checkbox';
    protected const ATTR_INPUT_SELECT      = 'select';
    protected const ATTR_INPUT_TEXT      = 'text';
    protected const ATTR_INPUT_NUMBER      = 'number';
    protected const ATTR_INPUT_TEXTAREA      = 'textarea';

    public const ATTRIBUTE_LIST = [];
    public const DTO = '';

    public static function getOperators(): array
    {
        $operators = [
            self::OP_EQUAL,
            self::OP_NOT_EQUAL,
            self::OP_IN,
            self::OP_NOT_IN,
            self::OP_LESS,
            self::OP_LESS_OR_EQUAL,

            self::OP_GREATER,
            self::OP_GREATER_OR_EQUAL,
            self::OP_BETWEEN,
            self::OP_NOT_BETWEEN,

            self::OP_BEGINS_WITH,
            self::OP_NOT_BEGINS_WITH,
            self::OP_NOT_CONTAINS,

            self::OP_ENDS_WITH,
            self::OP_NOT_ENDS_WITH,
            self::OP_IS_EMPTY,
            self::OP_IS_NOT_EMPTY,

            self::OP_IS_NULL,
            self::OP_IS_NOT_NULL,
        ];

        $operators[] = ['type' => self::OP_EQUAL2, 'optgroup' => 'custom', 'nb_inputs' => 1, 'multiple' => false, 'apply_to' => "['number', 'string']"];
        $operators[] = ['type' => self::OP_NOT_EQUAL2, 'optgroup' => 'custom', 'nb_inputs' => 1, 'multiple' => false, 'apply_to' => "['number', 'string']"];
        $operators[] = ['type' => '<=', 'optgroup' => 'custom', 'nb_inputs' => 1, 'multiple' => false, 'apply_to' => "['number', 'string']"];
        $operators[] = ['type' => '>=', 'optgroup' => 'custom', 'nb_inputs' => 1, 'multiple' => false, 'apply_to' => "['number', 'string']"];
        $operators[] = ['type' => '<', 'optgroup' => 'custom', 'nb_inputs' => 1, 'multiple' => false, 'apply_to' => "['number', 'string']"];
        $operators[] = ['type' => '>', 'optgroup' => 'custom', 'nb_inputs' => 1, 'multiple' => false, 'apply_to' => "['number', 'string']"];

        $operators[] = ['type' => self::OP_MATCH, 'optgroup' => 'custom', 'nb_inputs' => 1, 'multiple' => false, 'apply_to' => "['number', 'string']"];
        $operators[] = ['type' => self::OP_IN_ARRAY, 'optgroup' => 'Array', 'nb_inputs' => 1, 'multiple' => false, 'apply_to' => "['number', 'string']"];
        $operators[] = ['type' => self::OP_NOT_IN_ARRAY, 'optgroup' => 'Array', 'nb_inputs' => 1, 'multiple' => false, 'apply_to' => "['number', 'string']"];
        $operators[] = ['type' => self::OP_CONTAINS, 'optgroup' => 'Array', 'nb_inputs' => 1, 'multiple' => true, 'apply_to' => "['number', 'string']"];

        return $operators;
    }

    public static function getDTO(): string
    {
        return static::DTO;
    }

    public static function getAttributeList(): array
    {
        return static::ATTRIBUTE_LIST;
    }

    public static function getAttributes(): array
    {
        $attributes = [];

        foreach (static::ATTRIBUTE_LIST as $item) {
            $attributes[$item['field']] = $item['label'];
        }

        return $attributes;
    }

    public static function getDataForField(string $field): array
    {
        $data = [];

        foreach (static::getAttributeList() as $attr) {
            if ($field === $attr['field']) {
                $data = $attr;

                break;
            }
        }

        return $data;
    }
}
