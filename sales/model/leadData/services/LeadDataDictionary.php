<?php

namespace sales\model\leadData\services;

use yii\helpers\ArrayHelper;

/**
 * Class LeadDataDictionary
 */
class LeadDataDictionary
{
    public const KEY_KAYAKCLICKID = 'kayakclickid';

    public const KEY_LIST = [
        self::KEY_KAYAKCLICKID => 'KayakClickId',
    ];

    public static function getKeyName(string $key): string
    {
        return ArrayHelper::getValue(self::KEY_LIST, $key, '-');
    }
}
