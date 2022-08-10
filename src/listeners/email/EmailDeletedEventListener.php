<?php

namespace src\listeners\email;

use src\entities\email\events\EmailDeletedEvent;
use Yii;
use src\entities\email\Email;

/**
 * Class EmailDeletedEventListener
 *
 */
class EmailDeletedEventListener
{
    /**
     * @param EmailDeletedEvent $event
     */
    public function handle(EmailDeletedEvent $event): void
    {
        try {
            if (!Email::find()->where(['e_body_id' => $event->emailBody->embd_id])->exists()) {
                $event->emailBody->emailBlob->delete();
                $event->emailBody->delete();
            }
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:EmailDeletedEventListener');
        }
    }
}
