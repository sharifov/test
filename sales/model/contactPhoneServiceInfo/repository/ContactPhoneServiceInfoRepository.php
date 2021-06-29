<?php

namespace sales\model\contactPhoneServiceInfo\repository;

use sales\helpers\ErrorsToStringHelper;
use sales\model\contactPhoneList\entity\ContactPhoneList;
use sales\model\contactPhoneServiceInfo\entity\ContactPhoneServiceInfo;
use sales\services\phone\checkPhone\CheckPhoneService;

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
