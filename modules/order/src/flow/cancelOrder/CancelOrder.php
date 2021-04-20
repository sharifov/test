<?php

namespace modules\order\src\flow\cancelOrder;

use modules\hotel\models\HotelQuote;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderRepository;
use modules\order\src\entities\order\OrderStatusAction;
use modules\order\src\entities\orderContact\OrderContact;
use modules\order\src\entities\orderData\OrderData;
use modules\product\src\entities\productQuote\ProductQuote;
use sales\entities\cases\CaseCategory;
use sales\entities\cases\Cases;
use sales\helpers\setting\SettingHelper;
use sales\model\caseOrder\entity\CaseOrder;
use sales\repositories\cases\CasesRepository;
use sales\services\cases\CasesCreateService;
use sales\services\client\ClientCreateForm;
use sales\services\client\ClientManageService;

/**
 * Class CancelOrder
 *
 * @property OrderRepository $orderRepository
 * @property FreeCancelChecker $freeCancelChecker
 * @property FlightCanceler $flightCanceler
 * @property HotelCanceler $hotelCanceler
 * @property CasesRepository $casesRepository
 * @property ClientManageService $clientManageService
 * @property CasesCreateService $casesCreateService
 */
class CancelOrder
{
    private OrderRepository $orderRepository;
    private FreeCancelChecker $freeCancelChecker;
    private FlightCanceler $flightCanceler;
    private HotelCanceler $hotelCanceler;
    private CasesRepository $casesRepository;
    private ClientManageService $clientManageService;
    private CasesCreateService $casesCreateService;

    public function __construct(
        OrderRepository $orderRepository,
        FreeCancelChecker $freeCancelChecker,
        FlightCanceler $flightCanceler,
        HotelCanceler $hotelCanceler,
        CasesRepository $casesRepository,
        ClientManageService $clientManageService,
        CasesCreateService $casesCreateService
    ) {
        $this->orderRepository = $orderRepository;
        $this->freeCancelChecker = $freeCancelChecker;
        $this->flightCanceler = $flightCanceler;
        $this->hotelCanceler = $hotelCanceler;
        $this->casesRepository = $casesRepository;
        $this->clientManageService = $clientManageService;
        $this->casesCreateService = $casesCreateService;
    }

    public function cancel(string $gid): void
    {
        $order = $this->orderRepository->findByGid($gid);

        try {
            if (!$this->freeCancelChecker->can($order)) {
                throw new OrderUnavailableProcessingException($this->freeCancelChecker->message);
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
            $this->casesCreateService->createByCancelFailedOrder($order);
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
