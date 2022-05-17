<?php

/**
 * Created
 * User: alex.connor@techork.com
 * Date: 2021-04-30
 * Time: 12:50 AM
 */

namespace modules\abac\components;

use common\models\Department;
use common\models\Employee;
use common\models\Project;
use common\models\UserGroup;
use common\models\UserRole;

/**
 * Class AbacBaseModel
 * @package modules\abac\components
 */
class AbacBaseModel
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



    protected const ATTR_OBJ_AVAILABLE = [
        'optgroup' => self::OPTGROUP_ENV_DATA,
        'id' => 'env_available',
        'field' => 'env.available',
        'label' => 'Available for all',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'default_value' => true,
        'vertical' => true,
        //'validation' => ['allow_empty_value' => true],
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_USER_USERNAME = [
        'optgroup' => self::OPTGROUP_ENV_USER,
        'id' => 'env_username',
        'field' => 'env.user.username',
        'label' => 'Username',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_TEXT,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_IN, self::OP_NOT_IN, self::OP_MATCH]
    ];

    protected const ATTR_USER_ROLES = [
        'optgroup' => self::OPTGROUP_ENV_USER,
        'id' => 'env_user_roles',
        'field' => 'env.user.roles',
        'label' => 'User Roles',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_IN_ARRAY, self::OP_NOT_IN_ARRAY]
    ];

    protected const ATTR_USER_MULTI_ROLES = [
        'optgroup' => self::OPTGROUP_ENV_USER,
        'id' => 'env_user_multi_roles',
        'field' => 'env.user.roles',
        'label' => 'User Multi Roles',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => true,
        'operators' =>  [self::OP_CONTAINS]
    ];

    protected const ATTR_USER_PROJECTS = [
        'optgroup' => self::OPTGROUP_ENV_USER,
        'id' => 'env_user_projects',
        'field' => 'env.user.projects',
        'label' => 'User Projects',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_IN_ARRAY, self::OP_NOT_IN_ARRAY]
    ];

    protected const ATTR_USER_DEPARTMENTS = [
        'optgroup' => self::OPTGROUP_ENV_USER,
        'id' => 'env_user_departments',
        'field' => 'env.user.departments',
        'label' => 'User Departments',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_IN_ARRAY, self::OP_NOT_IN_ARRAY]
    ];

    protected const ATTR_USER_GROUPS = [
        'optgroup' => self::OPTGROUP_ENV_USER,
        'id' => 'env_user_groups',
        'field' => 'env.user.groups',
        'label' => 'User Groups',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_IN_ARRAY, self::OP_NOT_IN_ARRAY]
    ];

    protected const ATTR_PROJECT_KEY = [
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
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_IN, self::OP_NOT_IN],
//        'unique' => true,
//        'description' => 'This filter is "unique", it can be used only once',
        'icon' => 'fa fa-list',
    ];




    protected const ATTR_REQ_CONTROLLER = [
        'optgroup' => self::OPTGROUP_ENV_REQUEST,
        'id' => 'env_controller',
        'field' => 'env.req.controller',
        'label' => 'Controller',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_TEXT,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_IN, self::OP_NOT_IN, self::OP_MATCH]
    ];

    protected const ATTR_REQ_ACTION = [
        'optgroup' => self::OPTGROUP_ENV_REQUEST,
        'id' => 'env_action',
        'field' => 'env.req.action',
        'label' => 'Controller/Action',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_TEXT,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_IN, self::OP_NOT_IN, self::OP_MATCH]
    ];

    protected const ATTR_REQ_URL = [
        'optgroup' => self::OPTGROUP_ENV_REQUEST,
        'id' => 'env_url',
        'field' => 'env.req.url',
        'label' => 'URL',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_TEXT,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_MATCH]
    ];

    protected const ATTR_REQ_IP_ADDRESS = [
        'optgroup' => self::OPTGROUP_ENV_REQUEST,
        'id' => 'env_ip',
        'field' => 'env.req.ip',
        'label' => 'IP Address',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_TEXT,
        'placeholder' => '___.___.___.___',

//        'validation' => [
//            'format' => '/^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$/',
//            'messages' => [
//                'format' => 'The provided IP is not valid'
//            ]
//        ],

        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_MATCH]
    ];


    protected const ATTR_DT_DATE = [
        'optgroup' => self::OPTGROUP_ENV_DT,
        'id' => 'env_dt_date',
        'field' => 'env.dt.date',
        'label' => 'Date',
        'type' => self::ATTR_TYPE_DATE,
        'input' => self::ATTR_INPUT_TEXT,
        'placeholder' => '____-__-__',
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_IN, self::OP_NOT_IN, self::OP_MATCH]
    ];

    protected const ATTR_DT_TIME = [
        'optgroup' => self::OPTGROUP_ENV_DT,
        'id' => 'env_dt_time',
        'field' => 'env.dt.time',
        'label' => 'Time',
        'type' => self::ATTR_TYPE_TIME,
        'input' => self::ATTR_INPUT_TEXT,
        'placeholder' => '__:__',
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_IN, self::OP_NOT_IN, self::OP_MATCH]
    ];

    protected const ATTR_DT_YEAR = [
        'optgroup' => self::OPTGROUP_ENV_DT,
        'id' => 'env_dt_year',
        'field' => 'env.dt.year',
        'label' => 'Year',
        'placeholder' => '____',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_NUMBER,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, '>=', '<=', '>', '<', self::OP_IN, self::OP_NOT_IN],
        'validation' => ['min' => 2020, 'max' => 2030, 'step' => 1]
    ];

    protected const ATTR_DT_MONTH = [
        'optgroup' => self::OPTGROUP_ENV_DT,
        'id' => 'env_dt_month',
        'field' => 'env.dt.month',
        'label' => 'Month',
        'type' => self::ATTR_TYPE_INTEGER,
        //'input' => self::ATTR_INPUT_NUMBER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [ 1 => '1 - January',2 => '2 - February',3 => '3 - March',4 => '4 - April',5 => '5 - May',6 => '6 - June',
            7 => '7 - July',8 => '8 - August',9 => '9 - September',10 => '10 - October',11 => '11 - November',12 => '12 - December'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, '>=', '<=', '>', '<'], // , self::OP_IN, self::OP_NOT_IN
        //'validation' => ['min' => 1, 'max' => 12, 'step' => 1]
    ];

    protected const ATTR_DT_MONTH_NAME = [
        'optgroup' => self::OPTGROUP_ENV_DT,
        'id' => 'env_dt_month_name',
        'field' => 'env.dt.month_name',
        'label' => 'Month name',
        'type' => self::ATTR_TYPE_STRING,
        //'input' => self::ATTR_INPUT_TEXT,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [ 'Jan' => 'January', 'Feb' => 'February', 'Mar' => 'March',
            'Apr' => 'April', 'May' => 'May', 'Jun' => 'June',
            'Jul' => 'July', 'Aug' => 'August', 'Sep' => 'September',
            'Oct' => 'October', 'Nov' => 'November', 'Dec' => 'December'],
        'multiple' => true,
        //'placeholder' => 'Jan, Feb, Mar, Apr, May, Jun, Jul, Aug, Sep, Oct, Nov, Dec',
        'operators' =>  [self::OP_IN, self::OP_NOT_IN] // self::OP_EQUAL2, self::OP_NOT_EQUAL2,
    ];

    protected const ATTR_DT_DOW = [
        'optgroup' => self::OPTGROUP_ENV_DT,
        'id' => 'env_dt_dow',
        'field' => 'env.dt.dow',
        'label' => 'Day of Week',
        'type' => self::ATTR_TYPE_INTEGER,
        //'input' => self::ATTR_INPUT_NUMBER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [ 7 => '7 - Sunday', 1 => '1 - Monday', 2 => '2 - Tuesday',
            3 => '3 - Wednesday', 4 => '4 - Thursday', 5 => '5 - Friday', 6 => '6 - Saturday'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, '>=', '<=', '>', '<'] // self::OP_IN, self::OP_NOT_IN
    ];

    protected const ATTR_DT_DOW_NAME = [
        'optgroup' => self::OPTGROUP_ENV_DT,
        'id' => 'env_dt_dow_name',
        'field' => 'env.dt.dow_name',
        'label' => 'Day of Week Name',
        'type' => self::ATTR_TYPE_STRING,
        //'input' => self::ATTR_INPUT_TEXT,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => ['Sun' => 'Sunday', 'Mon' => 'Monday', 'Tue' => 'Tuesday',
            'Wed' => 'Wednesday', 'Thu' => 'Thursday', 'Fri' => 'Friday', 'Sat' => 'Saturday'],
        'multiple' => true,
        'operators' =>  [self::OP_IN, self::OP_NOT_IN] //self::OP_EQUAL2, self::OP_NOT_EQUAL2,
    ];

    protected const ATTR_DT_DAY = [
        'optgroup' => self::OPTGROUP_ENV_DT,
        'id' => 'env_dt_day',
        'field' => 'env.dt.day',
        'label' => 'Day',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_NUMBER,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, '>=', '<=', '>', '<', self::OP_IN, self::OP_NOT_IN],
        'validation' => ['min' => 1, 'max' => 31, 'step' => 1],
        'description' => 'This filter is "day"',
    ];

    protected const ATTR_DT_HOUR = [
        'optgroup' => self::OPTGROUP_ENV_DT,
        'id' => 'env_dt_hour',
        'field' => 'env.dt.hour',
        'label' => 'Hour',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_NUMBER,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, '>=', '<=', '>', '<', self::OP_IN, self::OP_NOT_IN],
        'validation' => ['min' => 0, 'max' => 23, 'step' => 1]
    ];


    protected const ATTR_DT_MIN = [
        'optgroup' => self::OPTGROUP_ENV_DT,
        'id' => 'env_dt_min',
        'field' => 'env.dt.min',
        'label' => 'Minutes',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_NUMBER,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, '>=', '<=', '>', '<', self::OP_IN, self::OP_NOT_IN],
        'validation' => ['min' => 0, 'max' => 59, 'step' => 1]
    ];

    protected const ATTRIBUTE_LIST = [
        self::ATTR_OBJ_AVAILABLE,
        self::ATTR_USER_USERNAME,
        // self::ATTR_USER_ROLES,
        // self::ATTR_USER_PROJECTS,
        // self::ATTR_USER_DEPARTMENTS,

        // self::ATTR_PROJECT_KEY,

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
        $attributeList = self::ATTRIBUTE_LIST;

        $ur = self::ATTR_USER_ROLES;
        $mur = self::ATTR_USER_MULTI_ROLES;
        $ug = self::ATTR_USER_GROUPS;
        $up = self::ATTR_USER_PROJECTS;
        $ud = self::ATTR_USER_DEPARTMENTS;

        $ur['values'] = self::getUserRoleList();
        $mur['values'] = $ur['values'];
        $ug['values'] = self::getUserGroupList();
        $up['values'] = self::getProjectList();
        $ud['values'] = self::getDepartmentList();

        $attributeList[] = $ur;
        $attributeList[] = $mur;
        $attributeList[] = $ug;
        $attributeList[] = $up;
        $attributeList[] = $ud;

        return $attributeList;
    }


    /**
     * @return array
     */
    protected static function getUserRoleList(): array
    {
        return UserRole::getEnvListWOCache();
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
    protected static function getUserGroupList(): array
    {
        return UserGroup::getEnvList();
    }

    /**
     * @return array
     */
    protected static function getDepartmentList(): array
    {
        return Department::getEnvList();
    }

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
}
