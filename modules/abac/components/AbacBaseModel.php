<?php

/**
 * Created
 * User: alex.connor@techork.com
 * Date: 2021-04-30
 * Time: 12:50 AM
 */

namespace modules\abac\components;

use common\models\Department;


/**
 * Class AbacBaseModel
 * @package modules\abac\components
 */
class AbacBaseModel
{
    public const ATTR_TYPE_INTEGER  = 'integer';
    public const ATTR_TYPE_STRING   = 'string';
    public const ATTR_TYPE_DOUBLE   = 'double';
    public const ATTR_TYPE_DATE   = 'date';
    public const ATTR_TYPE_TIME   = 'time';
    public const ATTR_TYPE_DATETIME   = 'datetime';
    public const ATTR_TYPE_BOOLEAN   = 'boolean';


    public const ATTR_INPUT_RADIO       = 'radio';
    public const ATTR_INPUT_CHECKBOX    = 'checkbox';
    public const ATTR_INPUT_SELECT      = 'select';
    public const ATTR_INPUT_TEXT      = 'text';
    public const ATTR_INPUT_NUMBER      = 'number';
    public const ATTR_INPUT_TEXTAREA      = 'textarea';


    private const OPTGROUP_ENV_USER = 'ENV - USER';
    private const OPTGROUP_ENV_DT    = 'ENV - DATE & TIME';
    private const OPTGROUP_ENV_REQUEST = 'ENV - REQUEST';
    private const OPTGROUP_ENV_PROJECT = 'ENV - PROJECT';


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


    public const ATTR_USER_USERNAME = [
        'optgroup' => self::OPTGROUP_ENV_USER,
        'id' => 'env_username',
        'field' => 'env.user.username',
        'label' => 'Username',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_TEXT,
        'operators' =>  [self::OP_EQUAL, self::OP_NOT_EQUAL, self::OP_IN, self::OP_NOT_IN, '==', '!=', self::OP_MATCH]
    ];

    public const ATTR_USER_ROLES = [
        'optgroup' => self::OPTGROUP_ENV_USER,
        'id' => 'env_user_roles',
        'field' => 'env.user.roles',
        'label' => 'User roles',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_TEXT,
        /*'input' => self::ATTR_INPUT_SELECT,
        'values' => [
            'admin' => 'admin',
            'agent' => 'agent',
        ],
        'multiple' => true,*/
        'operators' =>  [self::OP_IN_ARRAY, self::OP_NOT_IN_ARRAY]
    ];

    public const ATTR_USER_PROJECTS = [
        'optgroup' => self::OPTGROUP_ENV_USER,
        'id' => 'env_user_projects',
        'field' => 'env.user.projects',
        'label' => 'User projects',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_TEXT,
        /*'input' => self::ATTR_INPUT_SELECT,
        'values' => [
            'hop2' => 'Hop2',
            'kayak' => 'kayak',
        ],
        'multiple' => true,*/
        'operators' =>  [self::OP_IN_ARRAY, self::OP_NOT_IN_ARRAY]
    ];

    public const ATTR_USER_DEPARTMENTS = [
        'optgroup' => self::OPTGROUP_ENV_USER,
        'id' => 'env_user_departments',
        'field' => 'env.user.departments',
        'label' => 'User departments',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => Department::DEPARTMENT_LIST,
        'multiple' => true,
        'operators' =>  [self::OP_IN_ARRAY, self::OP_NOT_IN_ARRAY]
    ];

    public const ATTR_PROJECT_KEY = [
        'optgroup' => self::OPTGROUP_ENV_PROJECT,
        'id' => 'env_project_key',
        'field' => 'env.project.key',
        'label' => 'Project Key',
        'type'  => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_TEXT,
        //'value' => true // boolean
        //'multiple' => true,

        //'values' => Project::getList(),
        //'default_value' => 1,
        'operators' =>  ['==', '!=', self::OP_IN, self::OP_NOT_IN],
//        'unique' => true,
//        'description' => 'This filter is "unique", it can be used only once',
        'icon' => 'fa fa-list',
    ];




    public const ATTR_REQ_CONTROLLER = [
        'optgroup' => self::OPTGROUP_ENV_REQUEST,
        'id' => 'env_controller',
        'field' => 'env.req.controller',
        'label' => 'Controller',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_TEXT,
        'operators' =>  [self::OP_EQUAL, self::OP_NOT_EQUAL, self::OP_IN, self::OP_NOT_IN, '==', '!=', self::OP_MATCH]
    ];

    public const ATTR_REQ_ACTION = [
        'optgroup' => self::OPTGROUP_ENV_REQUEST,
        'id' => 'env_action',
        'field' => 'env.req.action',
        'label' => 'Controller/Action',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_TEXT,
        'operators' =>  [self::OP_EQUAL, self::OP_NOT_EQUAL, self::OP_IN, self::OP_NOT_IN, '==', '!=', self::OP_MATCH]
    ];

    public const ATTR_REQ_URL = [
        'optgroup' => self::OPTGROUP_ENV_REQUEST,
        'id' => 'env_url',
        'field' => 'env.req.url',
        'label' => 'URL',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_TEXT,
        'operators' =>  [self::OP_EQUAL, self::OP_NOT_EQUAL, '==', '!=', self::OP_MATCH]
    ];

    public const ATTR_REQ_IP_ADDRESS = [
        'optgroup' => self::OPTGROUP_ENV_REQUEST,
        'id' => 'env_ip',
        'field' => 'env.req.ip',
        'label' => 'IP Address',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_TEXT,
        'operators' =>  ['==', '!=', self::OP_MATCH]
    ];


    public const ATTR_DT_DATE = [
        'optgroup' => self::OPTGROUP_ENV_DT,
        'id' => 'env_dt_date',
        'field' => 'env.dt.date',
        'label' => 'Date',
        'type' => self::ATTR_TYPE_DATE,
        'input' => self::ATTR_INPUT_TEXT,
        'operators' =>  [self::OP_EQUAL, self::OP_NOT_EQUAL, self::OP_IN, self::OP_NOT_IN, '==', '!=', self::OP_MATCH]
    ];

    public const ATTR_DT_TIME = [
        'optgroup' => self::OPTGROUP_ENV_DT,
        'id' => 'env_dt_time',
        'field' => 'env.dt.time',
        'label' => 'Time',
        'type' => self::ATTR_TYPE_TIME,
        'input' => self::ATTR_INPUT_TEXT,
        'operators' =>  [self::OP_EQUAL, self::OP_NOT_EQUAL, self::OP_IN, self::OP_NOT_IN, '==', '!=', self::OP_MATCH]
    ];

    public const ATTR_DT_YEAR = [
        'optgroup' => self::OPTGROUP_ENV_DT,
        'id' => 'env_dt_year',
        'field' => 'env.dt.year',
        'label' => 'Year',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_NUMBER,
        'operators' =>  ['==', '!=', '>=', '<=', '>', '<']
    ];

    public const ATTR_DT_MONTH = [
        'optgroup' => self::OPTGROUP_ENV_DT,
        'id' => 'env_dt_month',
        'field' => 'env.dt.month',
        'label' => 'Month',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_NUMBER,
        'operators' =>  ['==', '!=', '>=', '<=', '>', '<']
    ];

    public const ATTR_DT_MONTH_NAME = [
        'optgroup' => self::OPTGROUP_ENV_DT,
        'id' => 'env_dt_month_name',
        'field' => 'env.dt.month_name',
        'label' => 'Month name',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_TEXT,
        'operators' =>  ['==', '!=']
    ];

    public const ATTR_DT_DOW = [
        'optgroup' => self::OPTGROUP_ENV_DT,
        'id' => 'env_dt_dow',
        'field' => 'env.dt.dow',
        'label' => 'Day of Week',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_NUMBER,
        'operators' =>  ['==', '!=', '>=', '<=', '>', '<']
    ];

    public const ATTR_DT_DOW_NAME = [
        'optgroup' => self::OPTGROUP_ENV_DT,
        'id' => 'env_dt_dow_name',
        'field' => 'env.dt.dow_name',
        'label' => 'Day of Week Name',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_TEXT,
        'operators' =>  ['==', '!=']
    ];

    public const ATTR_DT_DAY = [
        'optgroup' => self::OPTGROUP_ENV_DT,
        'id' => 'env_dt_day',
        'field' => 'env.dt.day',
        'label' => 'Day',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_NUMBER,
        'operators' =>  ['==', '!=', '>=', '<=', '>', '<']
    ];

    public const ATTR_DT_HOUR = [
        'optgroup' => self::OPTGROUP_ENV_DT,
        'id' => 'env_dt_hour',
        'field' => 'env.dt.hour',
        'label' => 'Hour',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_NUMBER,
        'operators' =>  ['==', '!=', '>=', '<=', '>', '<']
    ];


    public const ATTR_DT_MIN = [
        'optgroup' => self::OPTGROUP_ENV_DT,
        'id' => 'env_dt_min',
        'field' => 'env.dt.min',
        'label' => 'Minutes',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_NUMBER,
        'operators' =>  ['==', '!=', '>=', '<=', '>', '<']
    ];

    public const ATTRIBUTE_LIST = [
        self::ATTR_USER_USERNAME,
        self::ATTR_USER_ROLES,
        self::ATTR_USER_PROJECTS,
        self::ATTR_USER_DEPARTMENTS,
        self::ATTR_PROJECT_KEY,

        self::ATTR_REQ_CONTROLLER,
        self::ATTR_REQ_ACTION,
        self::ATTR_REQ_URL,
        self::ATTR_REQ_IP_ADDRESS,

        self::ATTR_DT_DATE,
        self::ATTR_DT_TIME,
        self::ATTR_DT_YEAR,
        self::ATTR_DT_MONTH,
        self::ATTR_DT_MONTH_NAME,
        self::ATTR_DT_DOW,
        self::ATTR_DT_DOW_NAME,
        self::ATTR_DT_DAY,
        self::ATTR_DT_HOUR,
        self::ATTR_DT_MIN,
    ];

    /**
     * @return array[]
     */
    public static function getDefaultAttributeList(): array
    {
        return self::ATTRIBUTE_LIST;
    }
}
