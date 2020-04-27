<?php

namespace sales\listeners\lead;

use common\components\Purifier;
use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use sales\events\lead\LeadCreatedCloneByUserEvent;
use sales\helpers\user\UserFinder;
use sales\repositories\NotFoundException;
use sales\repositories\user\UserRepository;
use Yii;

/**
 * Class LeadCreatedCloneByUserEventListener
 *
 * @property UserRepository $userRepository
 */
class LeadCreatedCloneByUserEventListener
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
     * @param LeadCreatedCloneByUserEvent $event
     */
    public function handle(LeadCreatedCloneByUserEvent $event): void
    {
        if (!$event->ownerOfOriginalLead) {
            return;
        }

        $lead = $event->lead;

        try {
            $owner = $this->userRepository->find($event->owner);
        } catch (NotFoundException $e) {
            Yii::warning('Not found employee (' . $event->owner . ')', 'LeadCreatedCloneByUserEventListener:notFoundOwner');
            return;
        }

        $subject = Yii::t('email', "Cloned Lead-{id} by {owner}", ['id' => $lead->clone_id, 'owner' => $owner->username]);

        try {
            $agent = UserFinder::find()->username;
        } catch (\Throwable $e) {
            $agent = 'System';
        }

        $body = Yii::t('email', "Agent {agent} cloned lead (Id: {clone_id}) with reason [{reason}]. New lead (Id: {lead_id})",
            [
                'agent' => $agent,
                'clone_id' => $lead->clone ? Purifier::createLeadShortLink($lead->clone) : 'not found',
                'reason' => $lead->description,
                'lead_id' => Purifier::createLeadShortLink($lead),
            ]);

        if ($ntf = Notifications::create($event->ownerOfOriginalLead, $subject, $body, Notifications::TYPE_INFO, true)) {
            //Notifications::socket($owner->id, null, 'getNewNotification', [], true);
            $dataNotification = (\Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
            Notifications::publish('getNewNotification', ['user_id' => $event->ownerOfOriginalLead], $dataNotification);
        } else {
            Yii::warning(
                'Not created Email notification to employee_id: ' . $event->ownerOfOriginalLead . ', lead: ' . $lead->id,
                self::class . ':createNotification:Lead:Clone'
            );
        }
    }
}
