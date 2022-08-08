<?php

namespace modules\taskList\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

class TaskListAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    public const NS = 'task-list/task-list/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';
    /** UI PERMISSION */
    public const UI_ASSIGN  = self::NS . 'ui/access';
    /** ACTION PERMISSION */
    public const ACT_MY_TASK_LIST = self::NS . 'act/my_task_list';
    public const OBJ_USER_TASK = self::NS . 'obj/user_task';

    /** ACTIONS */
    public const ACTION_ACCESS = 'access';
    public const ACTION_ADD_NOTE = 'addNote';
    public const ACTION_READ = 'read';

    public const ATTR_USER_TASK_OWNER = [
        'optgroup' => 'User Task',
        'id' => self::NS . 'isUserTaskOwner',
        'field' => 'isUserTaskOwner',
        'label' => 'Is UserTask Owner',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    /** ATTRIBUTE LIST */
    public const OBJECT_ATTRIBUTE_LIST = [
        self::OBJ_USER_TASK => [
            self::ATTR_USER_TASK_OWNER
        ]
    ];


    /**
     * OBJECT LIST
     * @return string[]
     */
    public static function getObjectList(): array
    {
        return [
            self::UI_ASSIGN => self::UI_ASSIGN,
            self::ACT_MY_TASK_LIST => self::ACT_MY_TASK_LIST,
            self::OBJ_USER_TASK => self::OBJ_USER_TASK,
        ];
    }

    /**
     * ACTION LIST
     * @return string[]
     */
    public static function getObjectActionList(): array
    {
        return [
            self::UI_ASSIGN => [self::ACTION_ACCESS],
            self::ACT_MY_TASK_LIST => [self::ACTION_ACCESS],
            self::OBJ_USER_TASK => [self::ACTION_ADD_NOTE, self::ACTION_READ],
        ];
    }

    /**
     * @return array
     */
    public static function getObjectAttributeList(): array
    {
        return self::OBJECT_ATTRIBUTE_LIST;
    }
}
