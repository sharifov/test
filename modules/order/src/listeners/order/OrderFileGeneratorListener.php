<?php

namespace modules\order\src\listeners\order;

use modules\attraction\src\jobs\AttractionQuotePdfJob;
use modules\flight\src\jobs\FlightQuotePdfJob;
use modules\hotel\src\jobs\HotelQuotePdfJob;
use modules\order\src\entities\order\events\OrderCompletedEvent;
use modules\order\src\entities\order\Order;
use modules\order\src\jobs\OrderGeneratorPdfJob;
use modules\rentCar\src\jobs\RentCarQuotePdfJob;
use sales\helpers\app\AppHelper;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Class OrderFileGeneratorListener
 */
class OrderFileGeneratorListener
{
    public function handle(OrderCompletedEvent $event): void
    {
        try {
            if (!$order = Order::findOne($event->orderId)) {
                throw new NotFoundHttpException('Order not found');
            }
            $orderGeneratorPdfJob = new OrderGeneratorPdfJob();
            $orderGeneratorPdfJob->orderId = $event->orderId;
            Yii::$app->queue_job->priority(10)->push($orderGeneratorPdfJob);

            foreach ($order->productQuotes as $productQuote) {
                if ($quote = $productQuote->getChildQuote()) {
                    if ($productQuote->isHotel()) {
                        $hotelQuotePdfJob = new HotelQuotePdfJob();
                        $hotelQuotePdfJob->hotelQuoteId = $quote->getId();
                        Yii::$app->queue_job->priority(10)->push($hotelQuotePdfJob);
                    } elseif ($productQuote->isFlight()) {
                        $flightQuotePdfJob = new FlightQuotePdfJob();
                        $flightQuotePdfJob->flightQuoteId = $quote->getId();
                        Yii::$app->queue_job->priority(10)->push($flightQuotePdfJob);
                    } elseif ($productQuote->isAttraction()) {
                        $attractionQuotePdfJob = new AttractionQuotePdfJob();
                        $attractionQuotePdfJob->quoteId = $quote->getId();
                        Yii::$app->queue_job->priority(10)->push($attractionQuotePdfJob);
                    } elseif ($productQuote->isRentCar()) {
                        $rentCarQuotePdfJob = new RentCarQuotePdfJob();
                        $rentCarQuotePdfJob->rentCarQuoteId = $quote->getId();
                        Yii::$app->queue_job->priority(10)->push($rentCarQuotePdfJob);
                    }
                }
            }
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'OrderFileGeneratorListener:Throwable');
        }
    }
}
