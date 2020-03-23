<?php

namespace sales\listeners\lead;

use common\models\Notifications;
use common\models\Quote;
use sales\events\lead\LeadBookedEvent;
use sales\repositories\NotFoundException;
use sales\repositories\user\UserRepository;
use Yii;

/**
 * Class LeadBookedNotificationsListener
 *
 * @property UserRepository $userRepository
 */
class LeadBookedNotificationsListener
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
     * @param LeadBookedEvent $event
     */
    public function handle(LeadBookedEvent $event): void
    {

        if (!$event->newOwnerId) {
            Yii::warning(
                'Not found ownerId on LeadBookedEvent Lead: ' . $event->lead->id,
                'LeadBookedNotificationsListener:ownerIdNotFound'
            );
            return;
        }

        try {
            $owner = $this->userRepository->find($event->newOwnerId);
        } catch (NotFoundException $e) {
            Yii::warning(
                'Not found owner for book lead: ' . $event->lead->id,
                'LeadBookedNotificationsListener:ownerNotFound'
            );
            return;
        }

        $lead = $event->lead;

        $host = Yii::$app->params['url_address'];

        $subject = Yii::t('email', 'Lead-{id} to BOOKED', ['id' => $lead->id]);

        $quote = Quote::find()->where(['lead_id' => $lead->id, 'status' => Quote::STATUS_APPLIED])->orderBy(['id' => SORT_DESC])->one();

        $body = Yii::t('email', "Your Lead (ID: {lead_id}) has been changed status to BOOKED! Booked quote UID: {quote_uid} {url}",
            [
                'lead_id' => $lead->id,
                'quote_uid' => $quote ? $quote->uid : '-',
                'url' => $host . '/lead/view/' . $lead->gid,
            ]);

        if ($ntf = Notifications::create($owner->id, $subject, $body, Notifications::TYPE_INFO, true)) {
            //Notifications::socket($owner->id, null, 'getNewNotification', [], true);
            Notifications::sendSocket('getNewNotification', ['user_id' => $owner->id]);
        } else {
            Yii::warning(
                'Not created Email notification to employee_id: ' . $owner->id . ', lead: ' . $lead->id,
                'LeadBookedNotificationsListener:createNotification'
            );
        }

    }

}
