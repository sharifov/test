<?php

namespace sales\listeners\lead;

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

        $lead = $event->lead;

        $host = \Yii::$app->params['url_address'];

        try {
            $owner = $this->userRepository->find($event->ownerId);
        } catch (NotFoundException $e) {
            Yii::$app->errorHandler->logException($e);
            Yii::warning('Not found employee (' . $event->ownerId . ')', 'LeadCreatedCloneByUserEventListener:notFoundOwner');
            return;
        }

        $subject = Yii::t('email', "Cloned Lead-{id} by {owner}", ['id' => $lead->clone_id, 'owner' => $owner->username]);

        try {
            $agent = UserFinder::find()->username;
        } catch (\Throwable $e) {
            $agent = 'System';
        }

        $body = Yii::t('email', "Agent {agent} cloned lead {clone_id} with reason [{reason}], url: {cloned_url}. New lead {lead_id} {url}",
            [
                'agent' => $agent,
                'url' => $host . '/lead/view/' . $lead->gid,
                'cloned_url' => $host . '/lead/view/' . ($lead->clone ? $lead->clone->gid : $lead->gid),
                'reason' => $lead->description,
                'lead_id' => $lead->id,
                'clone_id' => $lead->clone_id
            ]);

        if ($ntf = Notifications::create($owner->id, $subject, $body, Notifications::TYPE_INFO, true)) {
            //Notifications::socket($owner->id, null, 'getNewNotification', [], true);
            $dataNotification = (\Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
            Notifications::publish('getNewNotification', ['user_id' => $owner->id], $dataNotification);
        } else {
            Yii::warning(
                'Not created Email notification to employee_id: ' . $owner->id . ', lead: ' . $lead->id,
                self::class . ':createNotification:Lead:Clone'
            );
        }

    }

}
