<?php

namespace sales\model\clientDataKey\entity;

/**
 * Class ClientDataKeyDictionary
 *
 * Class for key constants
 */
class ClientDataKeyDictionary
{
    public const CACHE_TAG = 'client-data-key-tag-dependency';
    public const CACHE_DURATION = 60 * 2;

    public const APP_CALL_OUT_TOTAL_COUNT = 'app_call_out_total_count';

    public const KEY_LIST = [
        self::APP_CALL_OUT_TOTAL_COUNT => self::APP_CALL_OUT_TOTAL_COUNT,
    ];
}
