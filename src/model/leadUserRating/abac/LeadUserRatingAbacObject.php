<?php

namespace src\model\leadUserRating\abac;

use common\models\Lead;
use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

class LeadUserRatingAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'lead/';

    public const LEAD_RATING_FORM =  self::NS . 'set-user-rating';

    public const ACTION_VIEW  = 'view';
    public const ACTION_EDIT  = 'edit';

    public const OBJECT_LIST = [
        self::LEAD_RATING_FORM => self::LEAD_RATING_FORM,
    ];

    public const OBJECT_ACTION_LIST = [
        self::LEAD_RATING_FORM => [self::ACTION_VIEW, self::ACTION_EDIT],
    ];

    public const OBJECT_ATTRIBUTE_LIST = [
        self::LEAD_RATING_FORM => [
        ],
    ];

    protected const ATTR_LEAD_STATUS = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'status_id',
        'field' => 'status_id',
        'label' => 'Status',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN]
    ];

    protected const ATTR_USER_IS_OWNER = [
        'optgroup' => 'User',
        'id' => self::NS . 'is_owner',
        'field' => 'is_owner',
        'label' => 'Is owner',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [ 0 => false, 1 => true],
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN],
        'multiple' => false,
    ];

    public static function getObjectList(): array
    {
        return self::OBJECT_LIST;
    }

    public static function getObjectActionList(): array
    {
        return self::OBJECT_ACTION_LIST;
    }

    public static function getObjectAttributeList(): array
    {
        $attrLeadStatus = self::ATTR_LEAD_STATUS;
        $attrLeadStatus['values'] = Lead::getAllStatuses();
        $attributeList = self::OBJECT_ATTRIBUTE_LIST;
        $attributeList[self::LEAD_RATING_FORM][] = $attrLeadStatus;
        $attrIsOwner = self::ATTR_USER_IS_OWNER;
        $attributeList[self::LEAD_RATING_FORM][] = $attrIsOwner;
        return $attributeList;
    }
}
