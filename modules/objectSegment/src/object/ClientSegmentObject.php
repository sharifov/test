<?php

namespace modules\objectSegment\src\object;

use modules\objectSegment\components\ObjectSegmentBaseModel;
use modules\objectSegment\src\contracts\ObjectSegmentObjectInterface;

class ClientSegmentObject extends ObjectSegmentBaseModel implements ObjectSegmentObjectInterface
{
    protected const NS = 'client';

    protected const ATTR_COUNT_SOLD_LEADS = [
        'optgroup'  => 'Count Sold Leads',
        'id'        => self::NS . 'count_sold_leads',
        'field'     => 'count_sold_leads',
        'label'     => 'Count Sold Leads',
        'type'      => self::ATTR_TYPE_INTEGER,
        'input'     => self::ATTR_INPUT_NUMBER,
        'multiple'  => false,
        'operators' => [
            self::OP_LESS_OR_EQUAL,
            self::OP_GREATER_OR_EQUAL,
            self::OP_EQUAL,
            self::OP_EQUAL2,
            self::OP_GREATER,
            self::OP_LESS
        ]
    ];

    protected const OBJECT_ATTRIBUTE_LIST = [
        self::NS => [
            self::ATTR_COUNT_SOLD_LEADS,
        ],
    ];

    public static function getObjectAttributeList(): array
    {
        return self::OBJECT_ATTRIBUTE_LIST;
    }
}
