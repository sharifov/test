<?php

namespace sales\services\phone\checkPhone;

use sales\helpers\app\AppHelper;
use sales\helpers\setting\SettingHelper;
use common\components\CommunicationService;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class CheckPhoneService
 */
class CheckPhoneService
{
    public static function uidGenerator(string $phone): string
    {
        return md5($phone);
    }

    public static function cleanPhone(string $phone): string
    {
        return str_replace(['-', ' ', '++'], ['', '', '+'], $phone);
    }
}
