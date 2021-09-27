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
        return ContactPhoneList::find()
            ->innerJoin(ContactPhoneData::tableName(), 'cpd_cpl_id = cpl_id')
            ->where(['cpl_uid' => CheckPhoneService::uidGenerator($phone)])
            ->andWhere(['cpd_key' => ContactPhoneDataDictionary::KEY_ALLOW_LIST])
            ->andWhere(['cpd_value' => '1'])
            ->exists();
    }

    public static function isAutoCreateLeadOff(string $phone): bool
    {
        return ContactPhoneList::find()
            ->innerJoin(ContactPhoneData::tableName(), 'cpd_cpl_id = cpl_id')
            ->where(['cpl_uid' => CheckPhoneService::uidGenerator($phone)])
            ->andWhere(['cpd_key' => ContactPhoneDataDictionary::KEY_AUTO_CREATE_LEAD_OFF])
            ->andWhere(['cpd_value' => '1'])
            ->exists();
    }

    public static function isAutoCreateCaseOff(string $phone): bool
    {
        return ContactPhoneList::find()
            ->innerJoin(ContactPhoneData::tableName(), 'cpd_cpl_id = cpl_id')
            ->where(['cpl_uid' => CheckPhoneService::uidGenerator($phone)])
            ->andWhere(['cpd_key' => ContactPhoneDataDictionary::KEY_AUTO_CREATE_CASE_OFF])
            ->andWhere(['cpd_value' => '1'])
            ->exists();
    }
}
