<?php

namespace src\model\contactPhoneData\service;

use src\model\call\abac\CallAbacObject;
use Yii;
use yii\helpers\Html;

/**
 * Class ContactPhoneDataHelper
 */
class ContactPhoneDataHelper
{
    public static function getName(?string $key): string
    {
        return ContactPhoneDataDictionary::KEY_LIST[$key] ?? ($key ? 'Undefined' : '');
    }

    public static function getClass(?string $key): string
    {
        return ContactPhoneDataDictionary::KEY_LIST_CLASS[$key] ?? 'll-trash';
    }

    public static function getLabel(?string $key): string
    {
        return Html::tag('span', self::getName($key), ['class' => 'label ' . self::getClass($key), 'style' => 'font-size: 13px']);
    }

    public static function getLabelValue(?string $key, string $value): string
    {
        return Html::tag('span', $value, ['class' => 'label ' . self::getClass($key), 'style' => 'font-size: 13px']);
    }

    public static function accessAbacToKeyDataDropdown(): bool
    {
        return
            /** @abac CallAbacObject::ACT_DATA_ALLOW_LIST, CallAbacObject::ACTION_TOGGLE_DATA, Access to add/remove ContactPhoneData - key allow_list */
            (Yii::$app->abac->can(null, CallAbacObject::ACT_DATA_ALLOW_LIST, CallAbacObject::ACTION_TOGGLE_DATA))
            ||
            /** @abac CallAbacObject::ACT_DATA_IS_TRUSTED, CallAbacObject::ACTION_TOGGLE_DATA, Access to add/remove ContactPhoneData - key is_trusted */
            (Yii::$app->abac->can(null, CallAbacObject::ACT_DATA_IS_TRUSTED, CallAbacObject::ACTION_TOGGLE_DATA))
            ||
            /** @abac CallAbacObject::ACT_DATA_AUTO_CREATE_CASE_OFF, CallAbacObject::ACTION_TOGGLE_DATA, Access to add/remove ContactPhoneData - key auto_create_case_off */
            (Yii::$app->abac->can(null, CallAbacObject::ACT_DATA_AUTO_CREATE_CASE_OFF, CallAbacObject::ACTION_TOGGLE_DATA))
            ||
            /** @abac CallAbacObject::ACT_DATA_AUTO_CREATE_LEAD_OFF, CallAbacObject::ACTION_TOGGLE_DATA, Access to add/remove ContactPhoneData - key auto_create_lead_off */
            (Yii::$app->abac->can(null, CallAbacObject::ACT_DATA_AUTO_CREATE_LEAD_OFF, CallAbacObject::ACTION_TOGGLE_DATA))
            ||
            /** @abac CallAbacObject::ACT_DATA_INVALID, CallAbacObject::ACTION_TOGGLE_DATA, Access to add/remove ContactPhoneData - key invalid */
            (Yii::$app->abac->can(null, CallAbacObject::ACT_DATA_INVALID, CallAbacObject::ACTION_TOGGLE_DATA))
            ;
    }
}
