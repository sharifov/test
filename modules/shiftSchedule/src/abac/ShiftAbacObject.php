<?php

namespace modules\shiftSchedule\src\abac;

use common\models\UserGroup;
use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;

/**
 * Class ShiftAbacObject
 */
class ShiftAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'shift/shift/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** ACTION PERMISSION */
    public const ACT_USER_SHIFT_ASSIGN = self::NS . 'act/user_shift_assign';
    public const ACT_MY_SHIFT_SCHEDULE = self::NS . 'act/my_shift_schedule';
    public const ACT_USER_SHIFT_SCHEDULE = self::NS . 'act/user_shift_schedule';

    public const OBJ_USER_SHIFT_EVENT = self::NS . 'obj/user_shift_event';
    public const OBJ_USER_SHIFT_CALENDAR = self::NS . 'obj/user_shift_calendar';

    /** OBJECT LIST */
    public const OBJECT_LIST = [
        self::ACT_USER_SHIFT_ASSIGN => self::ACT_USER_SHIFT_ASSIGN,
        self::ACT_MY_SHIFT_SCHEDULE => self::ACT_MY_SHIFT_SCHEDULE,
        self::ACT_USER_SHIFT_SCHEDULE => self::ACT_USER_SHIFT_SCHEDULE,
        self::OBJ_USER_SHIFT_EVENT => self::OBJ_USER_SHIFT_EVENT,
        self::ALL => self::ALL,
        self::OBJ_USER_SHIFT_CALENDAR => self::OBJ_USER_SHIFT_CALENDAR,
    ];

    /** ACTIONS */
    public const ACTION_ACCESS = 'access';
    public const ACTION_UPDATE = 'update';

    public const ACTION_CREATE      = 'create';
    public const ACTION_READ        = 'read';
    public const ACTION_DELETE      = 'delete';
    public const ACTION_CREATE_ON_DOUBLE_CLICK = 'createOnDoubleClick';
    public const ACTION_VIEW_ALL_EVENTS = 'viewAllEvents';
    public const ACTION_GENERATE_EXAMPLE_DATA = 'generateExampleData';
    public const ACTION_GENERATE_USER_SCHEDULE = 'generateUserSchedule';
    public const ACTION_REMOVE_ALL_USER_SCHEDULE = 'removeAllUserSchedule';

    /** ACTION LIST */
    public const OBJECT_ACTION_LIST = [
        self::ACT_USER_SHIFT_ASSIGN => [self::ACTION_ACCESS, self::ACTION_UPDATE],
        self::ACT_MY_SHIFT_SCHEDULE => [
            self::ACTION_ACCESS,
            self::ACTION_GENERATE_EXAMPLE_DATA,
            self::ACTION_GENERATE_USER_SCHEDULE,
            self::ACTION_REMOVE_ALL_USER_SCHEDULE,
            ],
        self::ACT_USER_SHIFT_SCHEDULE => [self::ACTION_ACCESS],
        self::OBJ_USER_SHIFT_EVENT => [
            self::ACTION_CREATE,
            self::ACTION_READ,
            self::ACTION_UPDATE,
            self::ACTION_DELETE,
            self::ACTION_ACCESS,
            self::ACTION_CREATE_ON_DOUBLE_CLICK
        ],
        self::ALL => [
            self::ACTION_ACCESS,
            self::ACTION_CREATE,
            self::ACTION_READ,
            self::ACTION_UPDATE,
            self::ACTION_DELETE
        ],
        self::OBJ_USER_SHIFT_CALENDAR => [
            self::ACTION_VIEW_ALL_EVENTS
        ]
    ];

    public const ATTR_USER_GROUPS_FORM_FIELD = [
        'optgroup' => 'Form',
        'id' => self::NS . 'formSelectUserGroups',
        'field' => 'formSelectUserGroups',
        'label' => 'User Groups',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => true,
        'operators' =>  [self::OP_CONTAINS]
    ];

    public const ATTR_STATUS_FORM_FIELD = [
        'optgroup' => 'Form',
        'id' => self::NS . 'formSelectStatus',
        'field' => 'formSelectStatus',
        'label' => 'Status',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => true,
        'operators' =>  [self::OP_CONTAINS]
    ];

    public const ATTR_SCHEDULE_TYPE_FORM_FIELD = [
        'optgroup' => 'Form',
        'id' => self::NS . 'formSelectScheduleType',
        'field' => 'formSelectScheduleType',
        'label' => 'Schedule Type',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => true,
        'operators' =>  [self::OP_CONTAINS]
    ];

    /** ATTRIBUTE LIST */
    public const OBJECT_ATTRIBUTE_LIST = [
        self::OBJ_USER_SHIFT_EVENT => []
    ];

    /**
     * @return string[]
     */
    public static function getObjectList(): array
    {
        return self::OBJECT_LIST;
    }

    /**
     * @return string[]
     */
    public static function getObjectActionList(): array
    {
        return self::OBJECT_ACTION_LIST;
    }

    /**
     * @return \array[][]
     */
    public static function getObjectAttributeList(): array
    {
        $attributeList = self::OBJECT_ATTRIBUTE_LIST;

        $attrUserGroups = self::ATTR_USER_GROUPS_FORM_FIELD;
        $attrStatus = self::ATTR_STATUS_FORM_FIELD;
        $attrScheduleType = self::ATTR_SCHEDULE_TYPE_FORM_FIELD;

        $userGroups = UserGroup::getList();
        $attrUserGroups['values'] = $userGroups;

        $statusList = UserShiftSchedule::getStatusList();
        $attrStatus['values'] = $statusList;

        $scheduleTypeList = ShiftScheduleType::getList();
        $attrScheduleType['values'] = $scheduleTypeList;

        $attributeList[self::OBJ_USER_SHIFT_EVENT][] = $attrUserGroups;
        $attributeList[self::OBJ_USER_SHIFT_EVENT][] = $attrStatus;
        $attributeList[self::OBJ_USER_SHIFT_EVENT][] = $attrScheduleType;

        return $attributeList;
    }
}
