<?php

namespace sales\listeners\lead;

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

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param LeadFollowUpEvent $event
     */
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

        $host = Yii::$app->params['url_address'];

        $subject = Yii::t('email', "Lead-{id} to FOLLOW-UP", ['id' => $event->lead->id]);
        $body = Yii::t('email', 'Your Lead (ID: {lead_id}) has been changed status to FOLLOW-UP!
Reason: {reason}
{url}',
            [
                'lead_id' => $event->lead->id,
                'reason' => $event->reason ?: '-',
                'url' => $host . '/lead/view/' . $event->lead->gid,
            ]);

        if (Notifications::create($newOwner->id, $subject, $body, Notifications::TYPE_INFO, true)) {
            Notifications::socket($newOwner->id, null, 'getNewNotification', [], true);
        } else {
            Yii::warning(
                'Not created Email notification to employee_id: ' . $newOwner->id . ', lead: ' . $event->lead->id,
                'LeadFollowUpNotificationsListener:sendNotification'
            );
        }
    }

}
