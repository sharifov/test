<?php

namespace src\model\clientDataKey\entity;

/**
 * Class ClientDataKeyDictionary
 *
 * Class for key constants
 */
class ClientDataKeyDictionary
{
    public const CACHE_TAG = 'client-data-key-tag-dependency';
    public const CACHE_DURATION = 60;

    public const APP_CALL_OUT_TOTAL_COUNT = 'app_call_out_total_count';
    public const IS_SEND_TO_WEB_ENGAGE = 'is_sending_to_web_engage';
    public const CLIENT_RETURN = 'client_return';

    public const KEY_LIST = [
        self::APP_CALL_OUT_TOTAL_COUNT => self::APP_CALL_OUT_TOTAL_COUNT,
        self::IS_SEND_TO_WEB_ENGAGE => self::IS_SEND_TO_WEB_ENGAGE,
        self::CLIENT_RETURN => self::CLIENT_RETURN,
    ];
}
