<?php

namespace sales\listeners\lead;

use common\models\Airline;
use common\models\LeadFlightSegment;
use common\models\Notifications;
use common\models\Quote;
use sales\events\lead\LeadSoldEvent;
use sales\repositories\NotFoundException;
use sales\repositories\user\UserRepository;
use Yii;

/**
 * Class LeadSoldNotificationsListener
 *
 * @property UserRepository $userRepository
 */
class LeadSoldNotificationsListener
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
     * @param LeadSoldEvent $event
     */
    public function handle(LeadSoldEvent $event): void
    {
        if (!$event->newOwnerId) {
            Yii::warning(
                'Not found ownerId on LeadSoldEvent Lead: ' . $event->lead->id,
                'LeadSoldNotificationsListener:ownerIdNotFound'
            );
            return;
        }

        try {
            $owner = $this->userRepository->find($event->newOwnerId);
        } catch (NotFoundException $e) {
            Yii::warning(
                'Not found owner for sold lead: ' . $event->lead->id,
                'LeadSoldNotificationsListener:ownerNotFound'
            );
            return;
        }

        $lead = $event->lead;
        $quote = Quote::find()->where(['lead_id' => $lead->id, 'status' => Quote::STATUS_APPLIED])->orderBy(['id' => SORT_DESC])->one();
        $flightSegment = LeadFlightSegment::find()->where(['lead_id' => $lead->id])->orderBy(['id' => SORT_ASC])->one();
        $airlineName = '-';
        $profit = 0;
        if (!empty($quote)) {
            if ($airline = Airline::findOne(['iata' => $quote->main_airline_code])) {
                $airlineName = $airline->name;
            }
            $profit = number_format(Quote::countProfit($quote->id), 2);
        }

        $host = Yii::$app->params['url_address'];

        $subject = Yii::t('email', 'Lead-{id} to SOLD', ['id' => $lead->id]);

        $body = Yii::t('email', "Booked quote with UID : {quote_uid},
Source: {name},
Lead ID: {lead_id} ({url})
{name} made \${profit} on {airline} to {destination}",
            [
                'name' => $owner->username,
                'url' => $host . '/lead/view/' . $lead->gid,
                'lead_id' => $lead->id,
                'quote_uid' => $quote ? $quote->uid : '-',
                'destination' => $flightSegment ? $flightSegment->destination : '-',
                'airline' => $airlineName,
                'profit' => $profit
            ]);

        if (Notifications::create($owner->id, $subject, $body, Notifications::TYPE_INFO, true)) {
            // Notifications::socket($owner->id, null, 'getNewNotification', [], true);
            Notifications::sendSocket('getNewNotification', ['user_id' => $owner->id]);
        } else {
            Yii::warning(
                'Not created Email notification to employee_id: ' . $owner->id . ', lead: ' . $lead->id,
                'LeadSoldNotificationsListener:sendNotification'
            );
        }

    }

}
