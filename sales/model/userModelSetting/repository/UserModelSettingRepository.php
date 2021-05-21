<?php

namespace sales\model\userModelSetting\repository;

use sales\helpers\ErrorsToStringHelper;
use sales\model\userModelSetting\entity\UserModelSetting;

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
