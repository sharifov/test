<?php

namespace sales\listeners\lead;

use common\components\Purifier;
use frontend\widgets\notification\NotificationMessage;
use Yii;
use sales\repositories\NotFoundException;
use sales\events\lead\LeadFollowUpEvent;
use sales\repositories\user\UserRepository;
use common\models\Notifications;

/**
 * Class LeadFollowUpNotificationsListener
 *
 * @property UserRepository $userRepository
 */
class LeadFollowUpNotificationsListener
{

    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handle(LeadFollowUpEvent $event): void
    {
        if (!$event->newOwnerId || ($event->newOwnerId === $event->creatorId)) {
            return;
        }
        try {
            $newOwner = $this->userRepository->find($event->newOwnerId);
        } catch (NotFoundException $e) {
            Yii::warning(
                'Not found owner for follow up lead: ' . $event->lead->id,
                'LeadFollowUpNotificationsListener:ownerNotFound'
            );
            return;
        }

        $subject = Yii::t('email', "Lead-{id} to FOLLOW-UP", ['id' => $event->lead->id]);
        $body = Yii::t('email', 'Your Lead (Id: {lead_id}) has been changed status to FOLLOW-UP! Reason: {reason}',
            [
                'lead_id' => Purifier::createLeadShortLink($event->lead),
                'reason' => $event->reason ?: '-',
            ]);

        if ($ntf = Notifications::create($newOwner->id, $subject, $body, Notifications::TYPE_INFO, true)) {
            $dataNotification = (\Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
            Notifications::publish('getNewNotification', ['user_id' => $newOwner->id], $dataNotification);
        } else {
            Yii::warning(
                'Not created Email notification to employee_id: ' . $newOwner->id . ', lead: ' . $event->lead->id,
                'LeadFollowUpNotificationsListener:sendNotification'
            );
        }
    }
}
