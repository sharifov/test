<?php

namespace modules\shiftSchedule\src\abac;

use common\models\UserGroup;
use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
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
    public const ACT_SEND_SUPERVISION_NOTIFICATION = self::NS . 'act/send_supervision_notification';

    public const OBJ_USER_SHIFT_EVENT = self::NS . 'obj/user_shift_event';
    public const OBJ_USER_SHIFT_REQUEST_EVENT = self::NS . 'obj/user_shift_request_event';
    public const OBJ_USER_SHIFT_CALENDAR = self::NS . 'obj/user_shift_calendar';

    /** OBJECT LIST */
    public const OBJECT_LIST = [
        self::ACT_USER_SHIFT_ASSIGN => self::ACT_USER_SHIFT_ASSIGN,
        self::ACT_MY_SHIFT_SCHEDULE => self::ACT_MY_SHIFT_SCHEDULE,
        self::ACT_USER_SHIFT_SCHEDULE => self::ACT_USER_SHIFT_SCHEDULE,
        self::ACT_SEND_SUPERVISION_NOTIFICATION => self::ACT_SEND_SUPERVISION_NOTIFICATION,
        self::OBJ_USER_SHIFT_EVENT => self::OBJ_USER_SHIFT_EVENT,
        self::OBJ_USER_SHIFT_REQUEST_EVENT => self::OBJ_USER_SHIFT_REQUEST_EVENT,
        self::ALL => self::ALL,
        self::OBJ_USER_SHIFT_CALENDAR => self::OBJ_USER_SHIFT_CALENDAR,
    ];

    /** ACTIONS */
    public const ACTION_ACCESS = 'access';
    public const ACTION_UPDATE = 'update';

    public const ACTION_CREATE      = 'create';
    public const ACTION_READ        = 'read';
    public const ACTION_DELETE      = 'delete';
    public const ACTION_PERMANENTLY_DELETE      = 'permanentlyDelete';
    public const ACTION_CREATE_ON_DOUBLE_CLICK = 'createOnDoubleClick';
    public const ACTION_VIEW_ALL_EVENTS = 'viewAllEvents';
    public const ACTION_GENERATE_EXAMPLE_DATA = 'generateExampleData';
    public const ACTION_GENERATE_USER_SCHEDULE = 'generateUserSchedule';
    public const ACTION_REMOVE_ALL_USER_SCHEDULE = 'removeAllUserSchedule';
    public const ACTION_MULTIPLE_DELETE_EVENTS = 'multipleDeleteEvents';
    public const ACTION_MULTIPLE_UPDATE_EVENTS = 'multipleUpdateEvents';
    public const ACTION_VIEW_EVENT_LOG = 'viewEventLogs';

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
        self::ACT_SEND_SUPERVISION_NOTIFICATION => [self::ACTION_ACCESS],
        self::OBJ_USER_SHIFT_EVENT => [
            self::ACTION_CREATE,
            self::ACTION_READ,
            self::ACTION_UPDATE,
            self::ACTION_DELETE,
            self::ACTION_PERMANENTLY_DELETE,
            self::ACTION_ACCESS,
            self::ACTION_CREATE_ON_DOUBLE_CLICK
        ],
        self::OBJ_USER_SHIFT_REQUEST_EVENT => [
            self::ACTION_ACCESS,
        ],
        self::ALL => [
            self::ACTION_ACCESS,
            self::ACTION_CREATE,
            self::ACTION_READ,
            self::ACTION_UPDATE,
            self::ACTION_DELETE,
            self::ACTION_PERMANENTLY_DELETE
        ],
        self::OBJ_USER_SHIFT_CALENDAR => [
            self::ACTION_VIEW_ALL_EVENTS,
            self::ACTION_MULTIPLE_DELETE_EVENTS,
            self::ACTION_MULTIPLE_UPDATE_EVENTS,
            self::ACTION_VIEW_EVENT_LOG
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

    public const ATTR_REQUEST_STATUS_FORM_FIELD = [
        'optgroup' => 'Form',
        'id' => self::NS . 'formSelectRequestStatus',
        'field' => 'formSelectRequestStatus',
        'label' => 'Request Status',
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

    public const ATTR_SHIFT_EVENT_OWNER = [
        'optgroup' => 'Shift Event',
        'id' => self::NS . 'isEventOwner',
        'field' => 'isEventOwner',
        'label' => 'Is Event Owner',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    /** ATTRIBUTE LIST */
    public const OBJECT_ATTRIBUTE_LIST = [
        self::OBJ_USER_SHIFT_EVENT => [],
        self::OBJ_USER_SHIFT_EVENT => [
            self::ATTR_SHIFT_EVENT_OWNER
        ]
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
        $attrRequestStatus = self::ATTR_REQUEST_STATUS_FORM_FIELD;
        $attrScheduleType = self::ATTR_SCHEDULE_TYPE_FORM_FIELD;

        $userGroups = UserGroup::getList();
        $attrUserGroups['values'] = $userGroups;

        $statusList = UserShiftSchedule::getStatusList();
        $attrStatus['values'] = $statusList;

        $scheduleTypeList = ShiftScheduleType::getList();
        $attrScheduleType['values'] = $scheduleTypeList;

        $requestStatusList = ShiftScheduleRequest::getStatusList();
        $attrRequestStatus['values'] = $requestStatusList;

        $attributeList[self::OBJ_USER_SHIFT_EVENT][] = $attrUserGroups;
        $attributeList[self::OBJ_USER_SHIFT_EVENT][] = $attrStatus;
        $attributeList[self::OBJ_USER_SHIFT_EVENT][] = $attrScheduleType;
        $attributeList[self::OBJ_USER_SHIFT_REQUEST_EVENT][] = $attrRequestStatus;

        return $attributeList;
    }
}
