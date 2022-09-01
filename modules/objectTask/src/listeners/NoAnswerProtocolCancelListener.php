<?php

namespace modules\objectTask\src\listeners;

use modules\objectTask\src\scenarios\NoAnswer;
use modules\objectTask\src\services\ObjectTaskService;
use src\events\lead\LeadableEventInterface;
use src\helpers\app\AppHelper;
use Yii;

class NoAnswerProtocolCancelListener
{
    /**
     * @param LeadableEventInterface $event
     */
    public function handle(LeadableEventInterface $event): void
    {
        /** @fflag FFlag::FF_KEY_NO_ANSWER_PROTOCOL_ENABLE, No Answer protocol enable */
        if (\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_NO_ANSWER_PROTOCOL_ENABLE) === true) {
            $lead = $event->getLead();

            try {
                ObjectTaskService::cancelJobs(
                    NoAnswer::KEY,
                    ObjectTaskService::OBJECT_LEAD,
                    $lead->id,
                    'Canceled by lead status change'
                );
            } catch (\Throwable $e) {
                Yii::error(
                    AppHelper::throwableLog($e),
                    'NoAnswerProtocolCancelListener:handle'
                );
            }
        }
    }
}
