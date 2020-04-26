<?php

namespace sales\listeners\lead;

use common\components\Purifier;
use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use sales\events\lead\LeadOwnerChangedEvent;
use sales\repositories\NotFoundException;
use sales\repositories\user\UserRepository;
use Yii;

/**
 * Class LeadOwnerChangedNotificationsListener
 *
 * @property UserRepository $userRepository
 */
class LeadOwnerChangedNotificationsListener
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
     * @param LeadOwnerChangedEvent $event
     */
    public function handle(LeadOwnerChangedEvent $event): void
    {

        if (!$event->oldOwnerId || !$event->newOwnerId) {
            return;
        }

        try {
            $oldOwner = $this->userRepository->find($event->oldOwnerId);
        } catch (NotFoundException $e) {
            Yii::warning('Not found employee (' . $event->oldOwnerId . ')', 'LeadOwnerChangedNotificationsListener:sendNotification:oldOwner');
            return;
        }
        try {
            $newOwner = $this->userRepository->find($event->newOwnerId);
        } catch (NotFoundException $e) {
            Yii::warning('Not found employee (' . $event->newOwnerId . ')', 'LeadOwnerChangedNotificationsListener:sendNotification:newOwner');
            return;
        }

        $lead = $event->lead;

        $subject = Yii::t('email', 'Lead-{id} reassigned to ({username})', ['id' => $lead->id, 'username' => $newOwner->username]);

        $body = Yii::t('email', "Attention! Your Lead (Id: {lead_id}) has been reassigned to another agent ({name}).",
            [
                'lead_id' => Purifier::createLeadShortLink($lead),
                'name' => $newOwner->username,
            ]);

        if ($ntf = Notifications::create($oldOwner->id, $subject, $body, Notifications::TYPE_INFO, true)) {
            //Notifications::socket($oldOwner->id, null, 'getNewNotification', [], true);
            $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
            Notifications::publish('getNewNotification', ['user_id' => $oldOwner->id], $dataNotification);
        } else {
            Yii::warning(
                'Not created Email notification to employee_id: ' . $oldOwner->id . ', lead: ' . $lead->id,
                'LeadOwnerChangedNotificationsListener:sendNotification'
            );
        }
    }

}
