<?php

namespace modules\objectSegment\components;

use common\models\Department;
use common\models\Project;

class ObjectSegmentBaseModel
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


    protected const OPTGROUP_ENV_USER = 'ENV - USER';
    protected const OPTGROUP_ENV_DT    = 'ENV - DATE & TIME';
    protected const OPTGROUP_ENV_REQUEST = 'ENV - REQUEST';
    protected const OPTGROUP_ENV_PROJECT = 'ENV - PROJECT';
    protected const OPTGROUP_ENV_DATA = 'ENV - DATA';


    public const OP_EQUAL               = 'equal';
    public const OP_NOT_EQUAL           = 'not_equal';
    public const OP_IN                  = 'in';
    public const OP_NOT_IN              = 'not_in';
    public const OP_IN_ARRAY            = 'in_array';
    public const OP_NOT_IN_ARRAY        = 'not_in_array';
    public const OP_LESS                = 'less';
    public const OP_LESS_OR_EQUAL       = 'less_or_equal';
    public const OP_GREATER             = 'greater';
    public const OP_GREATER_OR_EQUAL    = 'greater_or_equal';
    public const OP_BETWEEN             = 'between';
    public const OP_NOT_BETWEEN         = 'not_between';
    public const OP_BEGINS_WITH         = 'begins_with';
    public const OP_NOT_BEGINS_WITH     = 'not_begins_with';
    public const OP_CONTAINS            = 'contains';
    public const OP_NOT_CONTAINS        = 'not_contains';
    public const OP_ENDS_WITH           = 'ends_with';
    public const OP_NOT_ENDS_WITH       = 'not_ends_with';
    public const OP_IS_EMPTY            = 'is_empty';
    public const OP_IS_NOT_EMPTY        = 'is_not_empty';
    public const OP_IS_NULL             = 'is_null';
    public const OP_IS_NOT_NULL         = 'is_not_null';

    public const OP_MATCH               = 'match';
    public const OP_EQUAL2              = '==';
    public const OP_NOT_EQUAL2          = '!=';

    /**
     * @return array
     */
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
            //self::OP_CONTAINS,
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

    /**
     * @return array
     */
    protected static function getProjectList(): array
    {
        return Project::getEnvList();
    }

    /**
     * @return array
     */
    protected static function getDepartmentList(): array
    {
        return Department::getEnvList();
    }

    /**
     * @return array[]
     */
    public static function getDefaultAttributeList(): array
    {
        return [];
    }
}
