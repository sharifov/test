<?php

namespace src\repositories\email;

use modules\featureFlag\FFlag;
use Yii;

class EmailRepositoryFactory
{
    public static function getRepository(): EmailRepositoryInterface
    {
        return Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_EMAIL_NORMALIZED_FORM_ENABLE) ?
            Yii::createObject(EmailRepository::class) :
            Yii::createObject(EmailOldRepository::class)
        ;
    }
}
