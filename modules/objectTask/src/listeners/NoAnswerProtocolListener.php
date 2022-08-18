<?php

namespace modules\objectTask\src\listeners;

use modules\objectTask\src\scenarios\NoAnswer;
use src\events\lead\LeadFollowUpEvent;

class NoAnswerProtocolListener
{
    /**
     * @param LeadFollowUpEvent $event
     */
    public function handle(LeadFollowUpEvent $event): void
    {
        /** @fflag FFlag::FF_KEY_NO_ANSWER_PROTOCOL_ENABLE, No Answer protocol enable */
        if (\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_NO_ANSWER_PROTOCOL_ENABLE) === true) {
            $noAnswer = new NoAnswer(
                $event->lead
            );

            $noAnswer->process();
        }
    }
}
