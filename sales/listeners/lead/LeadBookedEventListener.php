<?php

namespace sales\listeners\lead;

use common\models\Notifications;
use common\models\Quote;
use sales\events\lead\LeadBookedEvent;
use sales\repositories\NotFoundException;
use sales\repositories\user\UserRepository;
use Yii;

/**
 * Class LeadBookedEventListener
 * @property UserRepository $userRepository
 */
class LeadBookedEventListener
{

    private $userRepository;

    /**
     * LeadBookedEventListener constructor.
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

        $lead = $event->lead;
        $ownerId = $event->ownerId ?: $lead->employee_id;

        try {
            $owner = $this->userRepository->find($ownerId);
        } catch (NotFoundException $e) {
            Yii::warning(
                'Not found owner for booked lead: ' . $lead->id,
                self::class . ':ownerNotFound'
            );
            return;
        }

        $host = Yii::$app->params['url_address'];

        $subject = Yii::t('email', 'Lead-{id} to BOOKED', ['id' => $lead->id]);

        $quote = Quote::find()->where(['lead_id' => $lead->id, 'status' => Quote::STATUS_APPLIED])->orderBy(['id' => SORT_DESC])->one();

        $body = Yii::t('email', "Your Lead (ID: {lead_id}) has been changed status to BOOKED!
Booked quote UID: {quote_uid}
{url}",
            [
                'url' => $host . '/lead/view/' . $lead->gid,
                'lead_id' => $lead->id,
                'quote_uid' => $quote ? $quote->uid : '-'
            ]);

        if (Notifications::create($owner->id, $subject, $body, Notifications::TYPE_INFO, true)) {
            Notifications::socket($owner->id, null, 'getNewNotification', [], true);
        } else {
            Yii::warning(
                'Not created Email notification to employee_id: ' . $owner->id . ', lead: ' . $lead->id,
                self::class . ':createNotification'
            );
        }

    }

}