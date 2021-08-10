<?php

namespace modules\cases\src\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

class CasesAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'case/case/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** LOGIC PERMISSION */
    public const LOGIC_CLIENT_DATA   = self::NS . 'logic/client_data';

    /** UI PERMISSION */
    public const UI_BLOCK_EVENT_LOG_LIST  = self::NS . 'ui/block/event-log-list';
    public const UI_BTN_EVENT_LOG_VIEW     = self::NS . 'ui/btn/event-log-view';

    public const OBJECT_LIST = [
        self::LOGIC_CLIENT_DATA => self::LOGIC_CLIENT_DATA,
        self::UI_BLOCK_EVENT_LOG_LIST => self::UI_BLOCK_EVENT_LOG_LIST,
        self::UI_BTN_EVENT_LOG_VIEW => self::UI_BTN_EVENT_LOG_VIEW,
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_MASK    = 'mask';
    public const ACTION_UNMASK  = 'unmask';
    public const ACTION_ACCESS  = 'access';
    public const ACTION_READ  = 'read';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::LOGIC_CLIENT_DATA  => [self::ACTION_UNMASK],
        self::UI_BLOCK_EVENT_LOG_LIST  => [self::ACTION_ACCESS],
        self::UI_BTN_EVENT_LOG_VIEW  => [self::ACTION_READ]
    ];

    protected const ATTR_CASE_IS_OWNER = [
        'optgroup' => 'CASE',
        'id' => self::NS . 'is_owner',
        'field' => 'is_owner',
        'label' => 'Is Owner',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        //'validation' => ['allow_empty_value' => true],
        'operators' =>  [self::OP_EQUAL2]
    ];

    /** --------------- ATTRIBUTE LIST --------------------------- */
    public const OBJECT_ATTRIBUTE_LIST = [
        self::LOGIC_CLIENT_DATA    => [self::ATTR_CASE_IS_OWNER],
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
        return self::OBJECT_ATTRIBUTE_LIST;
    }
}
