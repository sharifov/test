<?php

namespace sales\model\contactPhoneServiceInfo\service;

use sales\model\contactPhoneList\entity\ContactPhoneList;
use sales\model\contactPhoneServiceInfo\entity\ContactPhoneServiceInfo;
use sales\model\contactPhoneServiceInfo\repository\ContactPhoneServiceInfoRepository;
use sales\services\phone\checkPhone\CheckPhoneService;

/**
 * Class ContactPhoneInfoService
 */
class ContactPhoneInfoService
{
    public static function getOrCreate(int $cplId, int $serviceId, array $phoneData): ContactPhoneServiceInfo
    {
        $contactPhoneServiceInfo = self::findByPk(
            $cplId,
            $serviceId
        );

        if (!$contactPhoneServiceInfo) {
            $contactPhoneServiceInfo = ContactPhoneServiceInfo::create(
                $cplId,
                $serviceId,
                $phoneData
            );
            (new ContactPhoneServiceInfoRepository())->save($contactPhoneServiceInfo);
        }
        return $contactPhoneServiceInfo;
    }

    public static function findByPk(int $cplId, int $serviceId): ?ContactPhoneServiceInfo
    {
        return ContactPhoneServiceInfo::findOne(['cpsi_cpl_id' => $cplId, 'cpsi_service_id' => $serviceId]);
    }

    public static function findByPhoneAndService(string $phone, int $serviceId): ?ContactPhoneServiceInfo
    {
        $uid = CheckPhoneService::uidGenerator($phone);
        /** @var ContactPhoneServiceInfo $phoneServiceInfo */
        $phoneServiceInfo = ContactPhoneServiceInfo::find()
            ->select(ContactPhoneServiceInfo::tableName() . '.*')
            ->innerJoin(ContactPhoneList::tableName(), 'cpl_id = cpsi_cpl_id')
            ->where(['cpsi_service_id' => $serviceId])
            ->andWhere(['cpl_uid' => $uid])
            ->one();
        return $phoneServiceInfo;
    }
}
