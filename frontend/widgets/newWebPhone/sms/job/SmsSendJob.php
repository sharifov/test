<?php

namespace frontend\widgets\newWebPhone\sms\job;

use common\models\Notifications;
use common\models\Sms;
use frontend\widgets\newWebPhone\sms\socket\Message;
use yii\queue\JobInterface;

/**
 * Class SmsSendJob
 *
 * @property int $smsId
 */
class SmsSendJob implements JobInterface
{
    public $smsId;

    public function execute($queue)
    {
        $sms = Sms::findOne($this->smsId);
        if (!$sms) {
            \Yii::error('Sms not found', 'SmsSendJob');
            return;
        }

        $smsResponse = $sms->sendSms();
        Notifications::publish('phoneWidgetSmsSocketMessage', ['user_id' => $sms->s_created_user_id], Message::updateStatus($sms));
    }
}
