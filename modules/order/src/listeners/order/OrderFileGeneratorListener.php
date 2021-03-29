<?php

namespace modules\order\src\listeners\order;

use modules\attraction\src\jobs\AttractionQuotePdfJob;
use modules\flight\src\jobs\FlightQuotePdfJob;
use modules\hotel\src\jobs\HotelQuotePdfJob;
use modules\order\src\entities\order\events\OrderCompletedEvent;
use modules\order\src\entities\order\Order;
use modules\order\src\jobs\OrderGeneratorPdfJob;
use modules\order\src\processManager\queue\Queue;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\rentCar\src\jobs\RentCarQuotePdfJob;
use sales\helpers\app\AppHelper;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Class OrderFileGeneratorListener
 *
 * @property Queue $queue
 */
class OrderFileGeneratorListener
{
    private Queue $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    public function handle(OrderCompletedEvent $event): void
    {
        try {
            if (!$order = Order::findOne($event->orderId)) {
                throw new NotFoundHttpException('Order not found');
            }
            $orderGeneratorPdfJob = new OrderGeneratorPdfJob();
            $orderGeneratorPdfJob->orderId = $event->orderId;
            $this->queue->priority(10)->push($orderGeneratorPdfJob);

            $quotes = ProductQuote::find()->byOrderId($order->or_id)->booked()->all();

            foreach ($quotes as $productQuote) {
                if ($quote = $productQuote->getChildQuote()) {
                    if ($productQuote->isHotel()) {
                        $hotelQuotePdfJob = new HotelQuotePdfJob();
                        $hotelQuotePdfJob->hotelQuoteId = $quote->getId();
                        $this->queue->priority(10)->push($hotelQuotePdfJob);
                    } elseif ($productQuote->isFlight()) {
                        $flightQuotePdfJob = new FlightQuotePdfJob();
                        $flightQuotePdfJob->flightQuoteId = $quote->getId();
                        $this->queue->priority(10)->push($flightQuotePdfJob);
                    } elseif ($productQuote->isAttraction()) {
                        $attractionQuotePdfJob = new AttractionQuotePdfJob();
                        $attractionQuotePdfJob->quoteId = $quote->getId();
                        $this->queue->priority(10)->push($attractionQuotePdfJob);
                    } elseif ($productQuote->isRentCar()) {
                        $rentCarQuotePdfJob = new RentCarQuotePdfJob();
                        $rentCarQuotePdfJob->rentCarQuoteId = $quote->getId();
                        $this->queue->priority(10)->push($rentCarQuotePdfJob);
                    }
                }
            }
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'OrderFileGeneratorListener:Throwable');
        }
    }
}
