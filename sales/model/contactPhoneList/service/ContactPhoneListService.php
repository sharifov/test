<?php

namespace sales\model\contactPhoneList\service;

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
        $uid = CheckPhoneService::uidGenerator($phone);
        if (!$contactPhoneList = ContactPhoneList::findOne(['cpl_uid' => $uid])) {
            $contactPhoneList = ContactPhoneList::create($phone, $title);
            (new ContactPhoneListRepository())->save($contactPhoneList);
        }
        return $contactPhoneList;
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
}
