<?php

namespace modules\objectSegment\src\contracts;

interface ObjectSegmentListContract
{
    public const OBJECT_SEGMENT_LIST_KEY_LEAD_TYPE_BUSINESS = 'lead_business_type';
    public const OBJECT_SEGMENT_LIST_KEY_CLIENT_RETURN = 'client_return';

    public const KEYS_LIST = [
        self::OBJECT_SEGMENT_LIST_KEY_LEAD_TYPE_BUSINESS,
        self::OBJECT_SEGMENT_LIST_KEY_CLIENT_RETURN
    ];
}
