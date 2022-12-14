<?php

namespace src\listeners\sms;

use common\components\purifier\Purifier;
use common\models\ClientPhone;
use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use src\events\sms\IncomingSmsCreatedByCaseTypeEvent;
use src\repositories\user\UserProjectParamsRepository;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class SmsCreatedByIncomingSupportNotificationListener
 *
 * @property UserProjectParamsRepository $projectParamsRepository
 */
class IncomingSmsCreatedByCaseTypeNotificationListener
{
    private $projectParamsRepository;

    public function __construct(UserProjectParamsRepository $projectParamsRepository)
    {
        $this->projectParamsRepository = $projectParamsRepository;
    }

    public function handle(IncomingSmsCreatedByCaseTypeEvent $event): void
    {
        if ($users = $this->projectParamsRepository->findUsersIdByPhone($event->userPhone)) {
            $clientName = $this->getClientName($event->clientPhone);
            foreach ($users as $userId) {
                if (
                    $ntf = Notifications::create(
                        $userId,
                        'New SMS from ' . $clientName,
                        nl2br(Html::encode($event->text))
                        . ($event->sms->sCase ? ' <br> Case (Id: ' . Purifier::createCaseShortLink($event->sms->sCase) . ')' : '')
                        . ($event->sms ? ' <br> SMS (Id: ' . Purifier::createSmsShortLink($event->sms) . ')' : '')
                        . ($event->sms->sCase ? ' <br> ' . $event->sms->sCase->project->name  : ''),
                        Notifications::TYPE_INFO,
                        true
                    )
                ) {
                    //Notifications::socket($userId, null, 'getNewNotification', ['sms_id' => $event->sms->s_id], true);
                    $dataNotification = (\Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                    Notifications::publish('getNewNotification', ['user_id' => $userId], $dataNotification);
                }
            }
        }

//        if ($event->caseId) {
            // Notifications::socket(null, $event->leadId, 'updateCommunication', ['sms_id' => $event->sms->s_id], true);
//            Notifications::sendSocket('getNewNotification', ['case_id' => $event->caseId], ['sms_id' => $event->sms->s_id]);
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
