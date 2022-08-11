<?php

namespace modules\objectSegment\src\contracts;

interface ObjectSegmentKeyContract
{
    public const TYPE_KEY_LEAD = 'lead';
    public const TYPE_KEY_CLIENT = 'client';

    public const TYPE_KEY_LIST = [
        self::TYPE_KEY_LEAD,
        self::TYPE_KEY_CLIENT,
    ];
}
