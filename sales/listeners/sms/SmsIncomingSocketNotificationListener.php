<?php

namespace sales\listeners\sms;

use common\models\Employee;
use common\models\Notifications;
use frontend\widgets\newWebPhone\sms\socket\Message;
use sales\model\sms\useCase\send\Contact;
use sales\repositories\user\UserProjectParamsRepository;
use sales\services\cases\CasesManageService;
use sales\services\sms\incoming\SmsIncomingEvent;

/**
 * Class SmsIncomingCaseNeedActionListener
 *
 * @property CasesManageService $service
 * @property UserProjectParamsRepository $projectParamsRepository
 */
class SmsIncomingSocketNotificationListener
{
    private $projectParamsRepository;

    public function __construct(UserProjectParamsRepository $projectParamsRepository)
    {
        $this->projectParamsRepository = $projectParamsRepository;
    }

    public function handle(SmsIncomingEvent $event): void
    {
        $sms = $event->sms;

        if (!$sms->client) {
            return;
        }

        if ($usersId = $this->projectParamsRepository->findUsersIdByPhone($event->sms->s_phone_to)) {
            foreach ($usersId as $userId) {
                if ($user = Employee::findOne($userId)) {
                    Notifications::publish('phoneWidgetSmsSocketMessage', ['user_id' => $user->id], Message::add($sms, $user, new Contact($event->sms->client)));
                }
            }
        }
    }
}
