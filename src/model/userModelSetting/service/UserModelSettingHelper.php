<?php

namespace src\model\userModelSetting\service;

use yii\bootstrap4\Html;

/**
 * Class UserModelSettingHelper
 */
class UserModelSettingHelper
{
    public static function getGridDefaultColumn(string $fieldKey): array
    {
        return [
            'label' => UserModelSettingDictionary::FIELD_LIST[$fieldKey],
            'format' => 'raw',
            'filter' => false,
            'enableSorting' => false,
        ];
    }
}
