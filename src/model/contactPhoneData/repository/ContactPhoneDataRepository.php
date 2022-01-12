<?php

namespace src\model\contactPhoneData\repository;

use src\helpers\ErrorsToStringHelper;
use src\model\contactPhoneData\entity\ContactPhoneData;

/**
 * Class ContactPhoneDataRepository
 */
class ContactPhoneDataRepository
{
    public function save(ContactPhoneData $model): ContactPhoneData
    {
        if (!$model->save()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($model));
        }
        return $model;
    }
}
