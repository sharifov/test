<?php

namespace sales\model\contactPhoneList\repository;

use sales\helpers\ErrorsToStringHelper;
use sales\model\contactPhoneList\entity\ContactPhoneList;

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
