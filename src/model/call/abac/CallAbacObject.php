<?php

namespace src\model\call\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

/**
 * Class CallAbacObject
 */
class CallAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'call/call/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** ACTION PERMISSION */
    public const ACT_ALL                        = self::NS . '*';
    public const ACT_DATA_ALLOW_LIST            = self::NS . 'act/data-allow-list';
    public const ACT_DATA_IS_TRUSTED            = self::NS . 'act/data-is-trusted';
    public const ACT_DATA_AUTO_CREATE_CASE_OFF  = self::NS . 'act/data-auto-create-case-off';
    public const ACT_DATA_AUTO_CREATE_LEAD_OFF  = self::NS . 'act/data-auto-create-lead-off';
    public const ACT_DATA_INVALID               = self::NS . 'act/data-invalid';
    public const OBJ_CALL_LOG                   = self::NS . 'obj/call-log';

    /** --------------- OBJECT LIST --------------------------- */
    public const OBJECT_LIST = [
        self::ACT_ALL                       => self::ACT_ALL,
        self::ACT_DATA_ALLOW_LIST           => self::ACT_DATA_ALLOW_LIST,
        self::ACT_DATA_IS_TRUSTED           => self::ACT_DATA_IS_TRUSTED,
        self::ACT_DATA_AUTO_CREATE_CASE_OFF => self::ACT_DATA_AUTO_CREATE_CASE_OFF,
        self::ACT_DATA_AUTO_CREATE_LEAD_OFF => self::ACT_DATA_AUTO_CREATE_LEAD_OFF,
        self::ACT_DATA_INVALID              => self::ACT_DATA_INVALID,
        self::OBJ_CALL_LOG                  => self::OBJ_CALL_LOG,
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_VIEW  = 'view';
    public const ACTION_CREATE  = 'create';
    public const ACTION_UPDATE  = 'update';
    public const ACTION_DELETE  = 'delete';
    public const ACTION_TOGGLE_DATA  = 'toggle_data';
    public const ACTION_LISTEN_RECORD  = 'listen_record';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::ACT_ALL                       => [self::ACTION_UPDATE, self::ACTION_TOGGLE_DATA],
        self::ACT_DATA_ALLOW_LIST           => [self::ACTION_TOGGLE_DATA],
        self::ACT_DATA_IS_TRUSTED           => [self::ACTION_TOGGLE_DATA],
        self::ACT_DATA_AUTO_CREATE_CASE_OFF => [self::ACTION_TOGGLE_DATA],
        self::ACT_DATA_AUTO_CREATE_LEAD_OFF => [self::ACTION_TOGGLE_DATA],
        self::ACT_DATA_INVALID              => [self::ACTION_TOGGLE_DATA],
        self::OBJ_CALL_LOG                  => [self::ACTION_VIEW, self::ACTION_CREATE, self::ACTION_UPDATE, self:: ACTION_DELETE, self::ACTION_LISTEN_RECORD]
    ];

    public const OBJECT_ATTRIBUTE_LIST = [];

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
