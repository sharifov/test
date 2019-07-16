<?php

namespace sales\listeners\lead;

use common\models\Notifications;
use sales\events\lead\LeadCreatedCloneEvent;
use sales\repositories\NotFoundException;
use sales\repositories\user\UserRepository;
use Yii;

/**
 * Class LeadCreatedCloneEventListener
 * @property UserRepository $userRepository
 */
class LeadCreatedCloneEventListener
{

    private $userRepository;

    /**
     * LeadCreatedCloneEventListener constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param LeadCreatedCloneEvent $event
     */
    public function handle(LeadCreatedCloneEvent $event): void
    {

        $lead = $event->lead;

        $host = \Yii::$app->params['url_address'];

        try {
            $owner = $this->userRepository->get($lead->employee_id);
        } catch (NotFoundException $e) {
            Yii::$app->errorHandler->logException($e);
            Yii::warning('Not found employee (' . $lead->employee_id . ')', static::class . ':notFoundOwner');
            return;
        }

        $agent = $owner->username;
        $subject = Yii::t('email', "Cloned Lead-{id} by {agent}", ['id' => $lead->clone_id, 'agent' => $agent]);
        $body = Yii::t('email', "Agent {agent} cloned lead {clone_id} with reason [{reason}], url: {cloned_url}.
New lead {lead_id}
{url}",
            [
                'agent' => $agent,
                'url' => $host . '/lead/view/' . $lead->gid,
                'cloned_url' => $host . '/lead/view/' . ($lead->clone ? $lead->clone->gid : $lead->gid),
                'reason' => $lead->description,
                'lead_id' => $lead->id,
                'clone_id' => $lead->clone_id
            ]);

        if (Notifications::create($owner->id, $subject, $body, Notifications::TYPE_INFO, true)) {
            Notifications::socket($owner->id, null, 'getNewNotification', [], true);
        } else {
            Yii::warning(
                'Not created Email notification to employee_id: ' . $owner->id . ', lead: ' . $lead->id,
                self::class . ':createNotification:Lead:Clone'
            );
        }

    }

}