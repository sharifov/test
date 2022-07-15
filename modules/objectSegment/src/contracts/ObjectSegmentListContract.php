<?php

namespace modules\objectSegment\src\contracts;

interface ObjectSegmentListContract
{
    public const OBJECT_SEGMENT_LIST_KEY_LEAD_TYPE_BUSINESS = 'lead_business_type';
    public const OBJECT_SEGMENT_LIST_KEY_CLIENT_NEW = 'client_new';
    public const OBJECT_SEGMENT_LIST_KEY_CLIENT_RETURN = 'client_return';
    public const OBJECT_SEGMENT_LIST_KEY_CLIENT_RETURN_DIAMOND = 'client_return_diamond';

    public const KEYS_LIST = [
        self::OBJECT_SEGMENT_LIST_KEY_LEAD_TYPE_BUSINESS,
    ];
}
