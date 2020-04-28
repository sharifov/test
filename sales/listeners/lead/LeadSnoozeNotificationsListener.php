<?php

namespace sales\listeners\lead;

use common\components\purifier\Purifier;
use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use sales\events\lead\LeadSnoozeEvent;
use sales\repositories\NotFoundException;
use sales\repositories\user\UserRepository;
use Yii;

/**
 * Class LeadSnoozeNotificationsListener
 *
 * @property UserRepository $userRepository
 */
class LeadSnoozeNotificationsListener
{

    private $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param LeadSnoozeEvent $event
     * @throws \yii\base\InvalidConfigException
     */
    public function handle(LeadSnoozeEvent $event): void
    {
        if (!$event->newOwnerId) {
            Yii::warning(
                'Not found ownerId on LeadSnoozeEvent Lead: ' . $event->lead->id,
                'LeadSnoozeNotificationsListener:ownerIdNotFound'
            );
            return;
        }

        try {
            $owner = $this->userRepository->find($event->newOwnerId);
        } catch (NotFoundException $e) {
            Yii::warning(
                'Not found owner for snooze lead: ' . $event->lead->id,
                'LeadSnoozeNotificationsListener:ownerNotFound'
            );
            return;
        }

        $lead = $event->lead;

        $subject = Yii::t('email', "Lead-{id} to SNOOZE", ['id' => $lead->id]);

        $body = Yii::t('email', "Your Lead (Id: {lead_id}) has been changed status to SNOOZE! Snooze for: {datetime}. Reason: {reason}",
            [
                'lead_id' => Purifier::createLeadShortLink($lead),
                'datetime' => Yii::$app->formatter->asDatetime(strtotime($event->snoozeFor)),
                'reason' => $event->reason ?: '-',
            ]);

        if ($ntf = Notifications::create($owner->id, $subject, $body, Notifications::TYPE_INFO, true)) {
            //Notifications::socket($owner->id, null, 'getNewNotification', [], true);
            $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
            Notifications::publish('getNewNotification', ['user_id' => $owner->id], $dataNotification);
        } else {
            Yii::warning(
                'Not created Email notification to employee_id: ' . $owner->id . ', lead: ' . $lead->id,
                'LeadSnoozeNotificationsListener:sendNotification'
            );
        }
    }

}
