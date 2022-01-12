<?php

namespace src\model\contactPhoneData\service;

use src\model\contactPhoneData\entity\ContactPhoneData;
use src\model\contactPhoneData\repository\ContactPhoneDataRepository;

/**
 * Class ContactPhoneDataService
 */
class ContactPhoneDataService
{
    public static function getOrCreate(int $cplId, string $key, string $value): ContactPhoneData
    {
        if (!$contactPhoneData = self::getByCplIdAndKey($cplId, $key)) {
            $contactPhoneData = ContactPhoneData::create($cplId, $key, $value);
        }
        $contactPhoneData->cpd_value = $value;
        return (new ContactPhoneDataRepository())->save($contactPhoneData);
    }

    public static function getByCplIdAndKey(int $cplId, string $key): ?ContactPhoneData
    {
        return ContactPhoneData::findOne(['cpd_cpl_id' => $cplId, 'cpd_key' => $key]);
    }

    public static function removeByCplIdAndKey(int $cplId, string $key): int
    {
        if ($contactPhoneData = self::getByCplIdAndKey($cplId, $key)) {
            return (int) $contactPhoneData->delete();
        }
        return 0;
    }
}
