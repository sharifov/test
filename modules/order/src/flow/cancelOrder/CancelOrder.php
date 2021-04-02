<?php

namespace modules\order\src\flow\cancelOrder;

use modules\hotel\models\HotelQuote;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderRepository;
use modules\order\src\entities\order\OrderStatusAction;
use modules\product\src\entities\productQuote\ProductQuote;

/**
 * Class CancelOrder
 *
 * @property OrderRepository $orderRepository
 * @property FreeCancelChecker $freeCancelChecker
 * @property FlightCanceler $flightCanceler
 * @property HotelCanceler $hotelCanceler
 */
class CancelOrder
{
    private OrderRepository $orderRepository;
    private FreeCancelChecker $freeCancelChecker;
    private FlightCanceler $flightCanceler;
    private HotelCanceler $hotelCanceler;

    public function __construct(
        OrderRepository $orderRepository,
        FreeCancelChecker $freeCancelChecker,
        FlightCanceler $flightCanceler,
        HotelCanceler $hotelCanceler
    ) {
        $this->orderRepository = $orderRepository;
        $this->freeCancelChecker = $freeCancelChecker;
        $this->flightCanceler = $flightCanceler;
        $this->hotelCanceler = $hotelCanceler;
    }

    public function cancel(string $gid): void
    {
        $order = $this->orderRepository->findByGid($gid);

        try {
            if (!$this->freeCancelChecker->can($order)) {
                throw new OrderUnavailableProcessingException();
            }

            $order->cancelProcessing('Cancel Order Flow', OrderStatusAction::CANCEL_FLOW, null);
            $this->orderRepository->save($order);

            foreach ($this->getFlightQuotesForCancel($order->productQuotes) as $productQuote) {
                $this->flightCanceler->cancel($productQuote, $order->or_project_id);
            }

            foreach ($this->getHotelQuotesForCancel($order->productQuotes) as $hotelQuote) {
                $this->hotelCanceler->cancel($hotelQuote);
            }

            $order->cancel('Cancel Order Flow', OrderStatusAction::CANCEL_FLOW, null);
            $this->orderRepository->save($order);
        } catch (\DomainException $e) {
            $this->processingFail($order);
            throw $e;
        }
    }

    private function processingFail(Order $order): void
    {
        $order->cancelFailed('Cancel Order Flow', OrderStatusAction::CANCEL_FLOW, null);
        $this->orderRepository->save($order);
        //todo create case
    }

    /**
     * @param ProductQuote[] $productQuotes
     * @return HotelQuote[]
     */
    private function getHotelQuotesForCancel(array $productQuotes): array
    {
        $quotes = [];
        foreach ($productQuotes as $quote) {
            if ($quote->isHotel() && $quote->isBooked()) {
                $quotes[] = $quote->childQuote;
            }
        }
        return $quotes;
    }

    /**
     * @param ProductQuote[] $productQuotes
     * @return ProductQuote[]
     */
    private function getFlightQuotesForCancel(array $productQuotes): array
    {
        $quotes = [];
        foreach ($productQuotes as $quote) {
            if ($quote->isFlight() && ($quote->isBooked() || $quote->isInProgress())) {
                $quotes[] = $quote;
            }
        }
        return $quotes;
    }
}
