<?php

namespace sales\listeners\lead;

use common\models\Lead;
use common\models\Notifications;
use common\models\Reason;
use sales\events\lead\LeadFollowUpEvent;
use sales\repositories\NotFoundException;
use sales\repositories\user\UserRepository;
use Yii;

/**
 * Class LeadFollowUpEventListener
 * @property UserRepository $userRepository
 */
class LeadFollowUpEventListener
{

    private $userRepository;

    /**
     * LeadFollowUpEventListener constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param LeadFollowUpEvent $event
     * @throws \yii\db\Exception
     */
    public function handle(LeadFollowUpEvent $event): void
    {

        $lead = $event->lead;

        if ($lead->status_description) {
            $reason = new Reason();
            $reason->lead_id = $lead->id;
            $reason->employee_id = $lead->employee_id;
            $reason->created = date('Y-m-d H:i:s');
            $reason->reason = $lead->status_description;
            $reason->save();
        }

        try {
            $owner = $this->userRepository->find($lead->employee_id);
        } catch (NotFoundException $e) {
            Yii::warning(
                'Not found owner for follow up lead: ' . $lead->id,
                self::class . ':ownerNotFound'
            );
            return;
        }

        $host = Yii::$app->params['url_address'];

        $subject = Yii::t('email', "Lead-{id} to FOLLOW-UP", ['id' => $lead->id]);
        $body = Yii::t('email', 'Your Lead (ID: {lead_id}) has been changed status to FOLLOW-UP!
Reason: {reason}
{url}',
            [
                'url' => $host . '/lead/view/' . $lead->gid,
                'reason' => $lead->status_description ?: '-',
                'lead_id' => $lead->id,
            ]);

        if (Notifications::create($owner->id, $subject, $body, Notifications::TYPE_INFO, true)) {
            // Notifications::socket($owner->id, null, 'getNewNotification', [], true);
            Notifications::sendSocket('getNewNotification', ['user_id' => $owner->id]);
        } else {
            Yii::warning(
                'Not created Email notification to employee_id: ' . $owner->id . ', lead: ' . $lead->id,
                self::class . ':sendNotification'
            );
        }
    }

}