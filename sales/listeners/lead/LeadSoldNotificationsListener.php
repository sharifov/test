<?php

namespace sales\listeners\lead;

use common\components\purifier\Purifier;
use common\models\Airline;
use common\models\LeadFlightSegment;
use common\models\Notifications;
use common\models\Quote;
use frontend\widgets\notification\NotificationMessage;
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

        $subject = Yii::t('email', 'Lead-{id} to SOLD', ['id' => $lead->id]);

        $body = Yii::t('email', "Booked quote with UID : {quote_uid}, Source: {name}, Lead: (Id: {lead_id}) {name} made \${profit} on {airline} to {destination}",
            [
                'name' => $owner->username,
                'lead_id' => Purifier::createLeadShortLink($lead),
                'quote_uid' => $quote ? $quote->uid : '-',
                'destination' => $flightSegment ? $flightSegment->destination : '-',
                'airline' => $airlineName,
                'profit' => $profit
            ]);

        if ($ntf = Notifications::create($owner->id, $subject, $body, Notifications::TYPE_INFO, true)) {
            // Notifications::socket($owner->id, null, 'getNewNotification', [], true);
            $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
            Notifications::publish('getNewNotification', ['user_id' => $owner->id], $dataNotification);
        } else {
            Yii::warning(
                'Not created Email notification to employee_id: ' . $owner->id . ', lead: ' . $lead->id,
                'LeadSoldNotificationsListener:sendNotification'
            );
        }

    }

}
