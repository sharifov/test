<?php

namespace sales\listeners\lead;

use common\models\Notifications;
use sales\events\lead\LeadOwnerChangedEvent;
use sales\repositories\NotFoundException;
use sales\repositories\user\UserRepository;
use Yii;

/**
 * Class LeadOwnerChangedEventListener
 * @property UserRepository $userRepository
 */
class LeadOwnerChangedEventListener
{

    private $userRepository;

    /**
     * LeadOwnerChangedEventListener constructor.
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
            $oldOwner = $this->userRepository->get($event->oldOwnerId);
        } catch (NotFoundException $e) {
            Yii::warning('Not found employee (' . $event->oldOwnerId . ')', self::class . ':sendNotification:oldOwner');
            return;
        }
        try {
            $newOwner = $this->userRepository->get($event->newOwnerId);
        } catch (NotFoundException $e) {
            Yii::warning('Not found employee (' . $event->newOwnerId . ')', self::class . ':sendNotification:newOwner');
            return;
        }

        $lead = $event->lead;

        $host = \Yii::$app->params['url_address'];

        $subject = Yii::t('email', 'Lead-{id} reassigned to ({username})', ['id' => $lead->id, 'username' => $newOwner->username]);

        $body = Yii::t('email', "Attention!
Your Lead (ID: {lead_id}) has been reassigned to another agent ({name}).
{url}",
            [
                'name' => $newOwner->username,
                'url' => $host . '/lead/view/' . $lead->gid,
                'lead_id' => $lead->id
            ]);

        if (Notifications::create($oldOwner->id, $subject, $body, Notifications::TYPE_INFO, true)) {
            Notifications::socket($oldOwner->id, null, 'getNewNotification', [], true);
        } else {
            Yii::warning(
                'Not created Email notification to employee_id: ' . $oldOwner->id . ', lead: ' . $lead->id,
                self::class . ':sendNotification'
            );
        }
    }

}