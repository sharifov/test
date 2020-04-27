<?php

namespace sales\listeners\sms;

use common\components\Purifier;
use common\models\ClientPhone;
use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use sales\events\sms\SmsCreatedByIncomingSalesEvent;
use sales\repositories\user\UserProjectParamsRepository;
use yii\helpers\Html;

/**
 * Class SmsCreatedByIncomingSalesNotificationListener
 *
 * @property UserProjectParamsRepository $projectParamsRepository
 */
class SmsCreatedByIncomingSalesNotificationListener
{

    private $projectParamsRepository;

    /**
     * @param UserProjectParamsRepository $projectParamsRepository
     */
    public function __construct(UserProjectParamsRepository $projectParamsRepository)
    {
        $this->projectParamsRepository = $projectParamsRepository;
    }

    /**
     * @param SmsCreatedByIncomingSalesEvent $event
     */
    public function handle(SmsCreatedByIncomingSalesEvent $event): void
    {
        if ($users = $this->projectParamsRepository->findUsersIdByPhone($event->userPhone)) {
            $clientName = $this->getClientName($event->clientPhone);
            foreach ($users as $userId) {
                if ($ntf = Notifications::create(
                    $userId,
                    'New SMS ' . $event->clientPhone,
                    'SMS from ' . $event->clientPhone . ' (' . $clientName . ') to ' . $event->userPhone . ' <br> ' . nl2br(Html::encode($event->text))
                    . ($event->sms->sLead ? '<br>Lead (Id: ' . Purifier::createLeadShortLink($event->sms->sLead) . ')' : ''),
                    Notifications::TYPE_INFO,
                    true)
                ) {
//                    Notifications::socket($userId, null, 'getNewNotification', ['sms_id' => $event->sms->s_id], true);
                    $dataNotification = (\Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                    Notifications::publish('getNewNotification', ['user_id' => $userId], $dataNotification);
                }
            }
        }

//        if ($event->leadId) {
             //Notifications::socket(null, $event->leadId, 'updateCommunication', ['sms_id' => $event->sms->s_id], true);
//            Notifications::sendSocket('getNewNotification', ['lead_id' => $event->leadId], ['sms_id' => $event->sms->s_id]);
//        }
    }

    /**
     * @param string|null $phone
     * @return string
     */
    private function getClientName(?string $phone): string
    {
        if (!$phone) {
            return '-';
        }
        $clientPhone = ClientPhone::find()->where(['phone' => $phone])->orderBy(['id' => SORT_DESC])->limit(1)->one();
        if ($clientPhone && $clientPhone->client) {
            return $clientPhone->client->full_name;
        }
        return '-';
    }
}
