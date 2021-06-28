<?php

namespace sales\model\contactPhoneServiceInfo\repository;

use sales\helpers\ErrorsToStringHelper;
use sales\model\contactPhoneServiceInfo\entity\ContactPhoneServiceInfo;

/**
 * Class ContactPhoneServiceInfoRepository
 */
class ContactPhoneServiceInfoRepository
{
    public function save(ContactPhoneServiceInfo $model): ContactPhoneServiceInfo
    {
        if (!$model->save()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($model));
        }
        return $model;
    }

    public static function findByPk(int $cplId, int $serviceId): ?ContactPhoneServiceInfo
    {
        return ContactPhoneServiceInfo::findOne(['cpsi_cpl_id' => $cplId, 'cpsi_service_id' => $serviceId]);
    }
}
