<?php

namespace src\model\contactPhoneList\repository;

use src\helpers\ErrorsToStringHelper;
use src\model\contactPhoneList\entity\ContactPhoneList;

/**
 * Class ContactPhoneListRepository
 */
class ContactPhoneListRepository
{
    public function save(ContactPhoneList $model): ContactPhoneList
    {
        if (!$model->save()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($model));
        }
        return $model;
    }
}
