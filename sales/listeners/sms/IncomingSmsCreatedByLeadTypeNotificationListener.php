<?php

namespace sales\listeners\sms;

use common\components\purifier\Purifier;
use common\models\ClientPhone;
use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use sales\events\sms\IncomingSmsCreatedByLeadTypeEvent;
use sales\repositories\user\UserProjectParamsRepository;
use yii\helpers\Html;

/**
 * Class IncomingSmsCreatedByLeadTypeNotificationListener
 *
 * @property UserProjectParamsRepository $projectParamsRepository
 */
class IncomingSmsCreatedByLeadTypeNotificationListener
{
    private $projectParamsRepository;

    public function __construct(UserProjectParamsRepository $projectParamsRepository)
    {
        $this->projectParamsRepository = $projectParamsRepository;
    }

    public function handle(IncomingSmsCreatedByLeadTypeEvent $event): void
    {
        if ($users = $this->projectParamsRepository->findUsersIdByPhone($event->userPhone)) {
            $clientName = $this->getClientName($event->clientPhone);
            foreach ($users as $userId) {
                if (
                    $ntf = Notifications::create(
                        $userId,
                        'New SMS from ' . $clientName,
                        nl2br(Html::encode($event->text))
                        . ($event->sms->sLead ? ' <br> Lead (Id: ' . Purifier::createLeadShortLink($event->sms->sLead) . ')' : '')
                        . ($event->sms ? ' <br> SMS (Id: ' . Purifier::createSmsShortLink($event->sms) . ')' : '')
                        . ($event->sms->sLead ? ' <br> ' . $event->sms->sLead->project->name  : ''),
                        Notifications::TYPE_INFO,
                        true
                    )
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
