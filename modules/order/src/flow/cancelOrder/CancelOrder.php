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
 */
class CancelOrder
{
    private OrderRepository $orderRepository;
    private FreeCancelChecker $freeCancelChecker;
    private FlightCanceler $flightCanceler;
    private HotelCanceler $hotelCanceler;
    private CasesRepository $casesRepository;
    private ClientManageService $clientManageService;

    public function __construct(
        OrderRepository $orderRepository,
        FreeCancelChecker $freeCancelChecker,
        FlightCanceler $flightCanceler,
        HotelCanceler $hotelCanceler,
        CasesRepository $casesRepository,
        ClientManageService $clientManageService
    ) {
        $this->orderRepository = $orderRepository;
        $this->freeCancelChecker = $freeCancelChecker;
        $this->flightCanceler = $flightCanceler;
        $this->hotelCanceler = $hotelCanceler;
        $this->casesRepository = $casesRepository;
        $this->clientManageService = $clientManageService;
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

            if (
                SettingHelper::isCreateCaseOnOrderCancelEnabled()
                &&
                $caseCategory = CaseCategory::find()->byKey(SettingHelper::getCaseCategoryKeyOnOrderCancel())->one()
            ) {
                $orderData = OrderData::findOne(['od_order_id' => $order->or_id]);

                $orderContact = OrderContact::find()->byOrderId($order->or_id)->last()->one();
                if (!$orderContact) {
                    throw new \DomainException('Cannot create client, order contact not found');
                }
                $client = $this->clientManageService->createBasedOnOrderContact($orderContact, $order->or_project_id);

                $case = Cases::createByApi(
                    $client->id,
                    $order->or_project_id,
                    $caseCategory->cc_dep_id,
                    $orderData->od_display_uid ?? null,
                    null,
                    null,
                    $caseCategory->cc_id
                );
                $this->casesRepository->save($case);

                $caseOrder = CaseOrder::create($case->cs_id, $order->or_id);
                $caseOrder->detachBehavior('user');
                if (!$caseOrder->save()) {
                    throw new \RuntimeException($caseOrder->getErrorSummary(true)[0]);
                }
            }
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
