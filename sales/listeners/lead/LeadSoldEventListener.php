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
 * Class LeadSoldEventListener
 * @property UserRepository $userRepository
 */
class LeadSoldEventListener
{

    private $userRepository;

    /**
     * LeadSoldEventListener constructor.
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

        $lead = $event->lead;

        try {
            $owner = $this->userRepository->find($lead->employee_id);
        } catch (NotFoundException $e) {
            Yii::warning(
                'Not found owner for sold lead: ' . $lead->id,
                self::class . ':ownerNotFound'
            );
            return;
        }

        $quote = Quote::find()->where(['lead_id' => $lead->id, 'status' => Quote::STATUS_APPLIED])->orderBy(['id' => SORT_DESC])->one();
        $flightSegment = LeadFlightSegment::find()->where(['lead_id' => $lead->id])->orderBy(['id' => SORT_ASC])->one();
        $airlineName = '-';
        $profit = 0;
        if (!empty($quote)) {
            $airline = Airline::findOne(['iata' => $quote->main_airline_code]);
            if (!empty($airline)) {
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
                self::class . ':sendNotification'
            );
        }

    }

}