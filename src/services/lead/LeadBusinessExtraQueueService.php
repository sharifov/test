<?php

namespace src\services\lead;

use modules\featureFlag\FFlag;
use modules\lead\src\abac\queue\LeadBusinessExtraQueueAbacObject;
use Yii;
use yii\web\ForbiddenHttpException;

/**
 * Class LeadBusinessExtraQueueService
 */
class LeadBusinessExtraQueueService
{
    public static function canAccess(): bool
    {
        /** @fflag FFlag::FF_KEY_BEQ_ENABLE, Business Extra Queue enable */
        if (Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_BEQ_ENABLE) === true) {
            /** @abac LeadBusinessExtraQueueAbacObject::UI_ACCESS, LeadBusinessExtraQueueAbacObject::ACTION_ACCESS, Access to take from business extra queue */
            return Yii::$app->abac->can(null, LeadBusinessExtraQueueAbacObject::UI_ACCESS, LeadBusinessExtraQueueAbacObject::ACTION_ACCESS);
        }

        return false;
    }
}
