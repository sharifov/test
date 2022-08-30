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
            Yii::$app->db->createCommand()->delete('email_case', ['ec_email_id' => $event->emailId])->execute();
            Yii::$app->db->createCommand()->delete('email_lead', ['el_email_id' => $event->emailId])->execute();
            Yii::$app->db->createCommand()->delete('email_client', ['ecl_email_id' => $event->emailId])->execute();
            Yii::$app->db->createCommand()->delete('email_relation', ['er_email_id' => $event->emailId])->execute();
            Yii::$app->db->createCommand()->delete('email_relation', ['er_reply_id' => $event->emailId])->execute();
            Yii::$app->db->createCommand()->delete('email_params', ['ep_email_id' => $event->emailId])->execute();
            Yii::$app->db->createCommand()->delete('email_log', ['el_email_id' => $event->emailId])->execute();
            Yii::$app->db->createCommand()->delete('email_contact', ['ec_email_id' => $event->emailId])->execute();
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:EmailDeletedEventListener');
        }
    }
}
