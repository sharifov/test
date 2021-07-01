<?php

namespace sales\model\contactPhoneData\repository;

use sales\helpers\ErrorsToStringHelper;
use sales\model\contactPhoneData\entity\ContactPhoneData;

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
