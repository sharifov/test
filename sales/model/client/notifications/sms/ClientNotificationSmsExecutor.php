<?php

namespace sales\model\client\notifications\sms;

use common\models\ClientPhone;
use sales\model\client\notifications\client\entity\ClientNotificationRepository;
use sales\model\client\notifications\sms\entity\ClientNotificationSmsList;
use sales\model\client\notifications\sms\entity\ClientNotificationSmsListRepository;
use sales\model\client\notifications\sms\entity\Status;
use sales\model\phoneList\entity\PhoneList;

/**
 * Class ClientNotificationSmsExecutor
 *
 * @property ClientNotificationSmsListRepository $clientNotificationSmsListRepository
 * @property ClientNotificationRepository $clientNotificationRepository
 */
class ClientNotificationSmsExecutor
{
    private ClientNotificationSmsListRepository $clientNotificationSmsListRepository;
    private ClientNotificationRepository $clientNotificationRepository;

    public function __construct(
        ClientNotificationRepository $clientNotificationRepository,
        ClientNotificationSmsListRepository $clientNotificationSmsListRepository
    ) {
        $this->clientNotificationSmsListRepository = $clientNotificationSmsListRepository;
        $this->clientNotificationRepository = $clientNotificationRepository;
    }

    public function execute(ClientNotificationSmsList $notification): void
    {
        if (!$notification->isNew()) {
            throw new \DomainException('Notification status invalid. Wait: "new", current: "' . Status::getName($notification->cnsl_status_id) . '" . ID: ' . $notification->cnsl_id);
        }

        $fromPhone = PhoneList::find()->select(['pl_phone_number'])->andWhere(['pl_id' => $notification->cnsl_from_phone_id])->scalar();
        if (!$fromPhone) {
            throw new \DomainException('Not found Phone From. PhoneListId: ' . $notification->cnsl_from_phone_id . ' SmsNotificationId: ' . $notification->cnsl_id);
        }

        $toPhone = ClientPhone::find()->select(['phone'])->andWhere(['id' => $notification->cnsl_to_client_phone_id])->scalar();
        if (!$toPhone) {
            throw new \DomainException('Not found Client Phone. ClientPhoneId: ' . $notification->cnsl_to_client_phone_id . ' SmsNotificationId: ' . $notification->cnsl_id);
        }

//        try {
//            $callSid = \Yii::$app->communication->makeSmsClientNotification(
//                $fromPhone,
//                $toPhone,
//                $notification->cnfl_message,
//                $notification->getData()->sayVoice,
//                $notification->getData()->sayLanguage,
//                $notification->cnfl_file_url,
//                [
//                    'client_id' => $notification->getData()->clientId,
//                    'project_id' => $notification->getData()->projectId,
//                    'case_id' => $notification->getData()->caseId,
//                    'phone_list_id' => $notification->cnfl_from_phone_id,
//                ]
//            );
//            $notification->processing($callSid, new \DateTimeImmutable());
//            $this->clientNotificationPhoneListRepository->save($notification);
//        } catch (\Throwable $e) {
//            $notification->error(new \DateTimeImmutable());
//            $this->clientNotificationPhoneListRepository->save($notification);
//            throw $e;
//        }
    }
}
