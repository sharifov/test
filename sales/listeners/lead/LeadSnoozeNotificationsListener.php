<?php

namespace sales\listeners\lead;

use common\models\Notifications;
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
        if (!$event->ownerId) {
            Yii::warning(
                'Not found ownerId on LeadSnoozeEvent Lead: ' . $event->lead->id,
                'LeadSnoozeNotificationsListener:ownerIdNotFound'
            );
            return;
        }

        try {
            $owner = $this->userRepository->find($event->ownerId);
        } catch (NotFoundException $e) {
            Yii::warning(
                'Not found owner for snooze lead: ' . $event->lead->id,
                'LeadSnoozeNotificationsListener:ownerNotFound'
            );
            return;
        }

        $lead = $event->lead;

        $host = Yii::$app->params['url_address'];

        $subject = Yii::t('email', "Lead-{id} to SNOOZE", ['id' => $lead->id]);

        $body = Yii::t('email', "Your Lead (ID: {lead_id}) has been changed status to SNOOZE!
Snooze for: {datetime}.
Reason: {reason}
{url}",
            [
                'lead_id' => $lead->id,
                'datetime' => Yii::$app->formatter->asDatetime(strtotime($event->snoozeFor)),
                'reason' => $event->description ?: '-',
                'url' => $host . '/lead/view/' . $lead->gid,
            ]);

        if (Notifications::create($owner->id, $subject, $body, Notifications::TYPE_INFO, true)) {
            Notifications::socket($owner->id, null, 'getNewNotification', [], true);
        } else {
            Yii::warning(
                'Not created Email notification to employee_id: ' . $owner->id . ', lead: ' . $lead->id,
                'LeadSnoozeNotificationsListener:sendNotification'
            );
        }
    }

}
