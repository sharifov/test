<?php

namespace sales\model\contactPhoneList\service;

use sales\model\contactPhoneData\entity\ContactPhoneData;
use sales\model\contactPhoneData\service\ContactPhoneDataDictionary;
use sales\model\contactPhoneList\entity\ContactPhoneList;
use sales\model\contactPhoneList\repository\ContactPhoneListRepository;
use sales\services\phone\checkPhone\CheckPhoneService;

/**
 * Class ContactPhoneListService
 */
class ContactPhoneListService
{
    public static function getOrCreate(string $phone, ?string $title = null): ContactPhoneList
    {
        if (!$contactPhoneList = self::getByPhone($phone)) {
            $contactPhoneList = ContactPhoneList::create($phone, $title);
            (new ContactPhoneListRepository())->save($contactPhoneList);
        }
        return $contactPhoneList;
    }

    public static function getByPhone(string $phone): ?ContactPhoneList
    {
        return ContactPhoneList::findOne(['cpl_uid' => CheckPhoneService::uidGenerator($phone)]);
    }

    public static function getWidthServiceInfo(string $phone): array
    {
        $uid = CheckPhoneService::uidGenerator($phone);
        return ContactPhoneList::find()
            ->innerJoinWith('contactPhoneServiceInfos')
            ->where(['cpl_uid' => $uid])
            ->asArray()
            ->all();
    }

    public static function isAllowList(string $phone): bool
    {
        return self::isExistByDataKey($phone, ContactPhoneDataDictionary::KEY_ALLOW_LIST);
    }

    public static function isTrust(string $phone): bool
    {
        return self::isExistByDataKey($phone, ContactPhoneDataDictionary::KEY_IS_TRUSTED);
    }

    public static function isAutoCreateLeadOff(string $phone): bool
    {
        return self::isExistByDataKey($phone, ContactPhoneDataDictionary::KEY_AUTO_CREATE_LEAD_OFF);
    }

    public static function isAutoCreateCaseOff(string $phone): bool
    {
        return self::isExistByDataKey($phone, ContactPhoneDataDictionary::KEY_AUTO_CREATE_CASE_OFF);
    }

    public static function isInvalid(string $phone): bool
    {
        return self::isExistByDataKey($phone, ContactPhoneDataDictionary::KEY_INVALID);
    }

    public static function isExistByDataKey(
        string $phone,
        string $key,
        string $value = ContactPhoneDataDictionary::DEFAULT_TRUE_VALUE
    ): bool {
        return ContactPhoneList::find()
            ->innerJoin(ContactPhoneData::tableName(), 'cpd_cpl_id = cpl_id')
            ->where(['cpl_uid' => CheckPhoneService::uidGenerator($phone)])
            ->andWhere(['cpd_key' => $key])
            ->andWhere(['cpd_value' => $value])
            ->exists();
    }
}
