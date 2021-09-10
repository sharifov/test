<?php

namespace sales\model\leadUserConversion\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

/**
 * Class LeadUserConversionAbacObject
 */
class LeadUserConversionAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'lead-user-conversion/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** ACTION PERMISSION */
    public const ACT_ALL     = self::NS . '/*';
    public const ACT_ADD     = self::NS . '/add';
    public const ACT_UPDATE  = self::NS . '/update';
    public const ACT_DELETE  = self::NS . '/delete';

    /** UI PERMISSION */
    public const UI_ALL             = self::NS . 'ui/*';
    public const UI_LIST_VIEW       = self::NS . 'ui/list_view';
    public const UI_BTN_ADD         = self::NS . 'ui/add';
    public const UI_BTN_UPDATE      = self::NS . 'ui/update';
    public const UI_BTN_DELETE      = self::NS . 'ui/delete';

    /** --------------- OBJECT LIST --------------------------- */
    public const OBJECT_LIST = [
        self::ACT_ALL       => self::ACT_ALL,
        self::ACT_ADD       => self::ACT_ADD,
        self::ACT_UPDATE    => self::ACT_UPDATE,
        self::ACT_DELETE    => self::ACT_DELETE,

        self::UI_ALL            => self::UI_ALL,
        self::UI_LIST_VIEW      => self::UI_LIST_VIEW,
        self::UI_BTN_ADD        => self::UI_BTN_ADD,
        self::UI_BTN_UPDATE     => self::UI_BTN_UPDATE,
        self::UI_BTN_DELETE     => self::UI_BTN_DELETE,
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_ADD     = 'add';
    public const ACTION_UPDATE  = 'update';
    public const ACTION_DELETE  = 'delete';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::ACT_ALL       => [self::ACTION_ADD, self::ACTION_UPDATE, self::ACTION_DELETE],
        self::ACT_ADD       => [self::ACTION_ADD],
        self::ACT_UPDATE    => [self::ACTION_UPDATE],
        self::ACT_DELETE    => [self::ACTION_DELETE],
    ];

    protected const ATTR_LEAD_IS_OWNER = [
        'optgroup' => 'LEAD',
        'id' => self::NS . 'is_owner',
        'field' => 'is_owner',
        'label' => 'Is Owner',

        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
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
        $attributeList = self::OBJECT_ATTRIBUTE_LIST;

        return $attributeList;
    }
}
