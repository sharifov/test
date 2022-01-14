<?php

namespace src\model\userModelSetting\repository;

use src\helpers\ErrorsToStringHelper;
use src\model\userModelSetting\entity\UserModelSetting;

/**
 * Class UserModelSettingRepository
 */
class UserModelSettingRepository
{
    public function save(UserModelSetting $model): int
    {
        if (!$model->save()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($model));
        }
        return $model->ums_id;
    }
}
