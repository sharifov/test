<?php

namespace src\services\phone\checkPhone;

use src\helpers\app\AppHelper;
use src\helpers\setting\SettingHelper;
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
        return hash('md4', $phone);
    }

    public static function cleanPhone(string $phone): string
    {
        return str_replace(['-', ' ', '++'], ['', '', '+'], $phone);
    }
}
