<?php

namespace src\model\contactPhoneServiceInfo\repository;

use src\helpers\ErrorsToStringHelper;
use src\model\contactPhoneList\entity\ContactPhoneList;
use src\model\contactPhoneServiceInfo\entity\ContactPhoneServiceInfo;
use src\services\phone\checkPhone\CheckPhoneService;

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
}
