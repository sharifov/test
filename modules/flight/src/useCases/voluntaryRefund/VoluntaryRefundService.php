<?php

namespace modules\flight\src\useCases\voluntaryRefund;

use modules\flight\src\entities\flightQuoteTicketRefund\FlightQuoteTicketRefund;
use modules\flight\src\entities\flightQuoteTicketRefund\FlightQuoteTicketRefundRepository;
use modules\flight\src\useCases\voluntaryRefund\manualCreate\VoluntaryRefundCreateForm;
use modules\flight\src\useCases\voluntaryRefund\manualUpdate\VoluntaryRefundUpdateForm;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\orderRefund\OrderRefund;
use modules\order\src\entities\orderRefund\OrderRefundRepository;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteObjectRefund\ProductQuoteObjectRefund;
use modules\product\src\entities\productQuoteObjectRefund\ProductQuoteObjectRefundRepository;
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionsQuery;
use modules\product\src\entities\productQuoteOptionRefund\ProductQuoteOptionRefund;
use modules\product\src\entities\productQuoteOptionRefund\ProductQuoteOptionRefundRepository;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefund;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundRepository;
use src\services\CurrencyHelper;
use src\services\TransactionManager;
use yii\helpers\Json;

/**
 * Class VoluntaryRefundService
 * @package modules\flight\src\useCases\voluntaryRefund
 *
 * @property OrderRefundRepository $orderRefundRepository
 * @property ProductQuoteRefundRepository $productQuoteRefundRepository
 * @property FlightQuoteTicketRefundRepository $flightQuoteTicketRefundRepository
 * @property ProductQuoteObjectRefundRepository $productQuoteObjectRefundRepository
 * @property ProductQuoteOptionRefundRepository $productQuoteOptionRefundRepository
 * @property TransactionManager $transactionManager
 */
class VoluntaryRefundService
{
    private OrderRefundRepository $orderRefundRepository;
    private ProductQuoteRefundRepository $productQuoteRefundRepository;
    private FlightQuoteTicketRefundRepository $flightQuoteTicketRefundRepository;
    private ProductQuoteObjectRefundRepository $productQuoteObjectRefundRepository;
    private ProductQuoteOptionRefundRepository $productQuoteOptionRefundRepository;
    private TransactionManager $transactionManager;

    public function __construct(
        OrderRefundRepository $orderRefundRepository,
        ProductQuoteRefundRepository $productQuoteRefundRepository,
        FlightQuoteTicketRefundRepository $flightQuoteTicketRefundRepository,
        ProductQuoteObjectRefundRepository $productQuoteObjectRefundRepository,
        ProductQuoteOptionRefundRepository $productQuoteOptionRefundRepository,
        TransactionManager $transactionManager
    ) {
        $this->orderRefundRepository = $orderRefundRepository;
        $this->productQuoteRefundRepository = $productQuoteRefundRepository;
        $this->flightQuoteTicketRefundRepository = $flightQuoteTicketRefundRepository;
        $this->productQuoteObjectRefundRepository = $productQuoteObjectRefundRepository;
        $this->productQuoteOptionRefundRepository = $productQuoteOptionRefundRepository;
        $this->transactionManager = $transactionManager;
    }

    public function createManual(Order $order, int $caseId, ProductQuote $originProductQuote, VoluntaryRefundCreateForm $form): void
    {
        $this->transactionManager->wrap(function () use ($order, $caseId, $originProductQuote, $form) {
            $orderRefund = OrderRefund::createByVoluntaryRefund(
                OrderRefund::generateUid(),
                $order->or_id,
                $order->or_app_total,
                CurrencyHelper::convertToBaseCurrency($form->getRefundForm()->totalAirlinePenalty, $order->orClientCurrency->cur_base_rate),
                CurrencyHelper::convertToBaseCurrency($form->getRefundForm()->totalProcessingFee, $order->orClientCurrency->cur_base_rate),
                CurrencyHelper::convertToBaseCurrency($form->getRefundForm()->totalRefundable, $order->orClientCurrency->cur_base_rate),
                $order->or_client_currency,
                $order->or_client_currency_rate,
                $order->or_client_total,
                $form->getRefundForm()->totalAirlinePenalty,
                $form->getRefundForm()->totalProcessingFee,
                $form->getRefundForm()->totalRefundable,
                $caseId
            );
            $orderRefund->new();
            $this->orderRefundRepository->save($orderRefund);

            $productQuoteRefund = ProductQuoteRefund::createByVoluntaryRefund(
                $orderRefund->orr_id,
                $originProductQuote->pq_id,
                CurrencyHelper::convertToBaseCurrency($form->getRefundForm()->totalPaid, $order->orClientCurrency->cur_base_rate),
                CurrencyHelper::convertToBaseCurrency($form->getRefundForm()->totalProcessingFee, $order->orClientCurrency->cur_base_rate),
                CurrencyHelper::convertToBaseCurrency($form->getRefundForm()->totalRefundable, $order->orClientCurrency->cur_base_rate),
                CurrencyHelper::convertToBaseCurrency($form->getRefundForm()->totalAirlinePenalty, $order->orClientCurrency->cur_base_rate),
                $form->getRefundForm()->currency,
                $order->or_client_currency_rate,
                $form->getRefundForm()->totalPaid,
                $form->getRefundForm()->totalAirlinePenalty,
                $form->getRefundForm()->totalProcessingFee,
                $form->getRefundForm()->totalRefundable,
                $caseId,
                null,
                $form->toArray(),
                CurrencyHelper::convertToBaseCurrency($form->getRefundForm()->refundCost, $order->orClientCurrency->cur_base_rate),
                $form->getRefundForm()->refundCost,
                $form->expirationDate
            );
            $productQuoteRefund->new();
            $this->productQuoteRefundRepository->save($productQuoteRefund);

            foreach ($form->getRefundForm()->ticketForms as $ticketForm) {
                $flightQuoteTicketRefund = FlightQuoteTicketRefund::create($ticketForm->number, null);
                $this->flightQuoteTicketRefundRepository->save($flightQuoteTicketRefund);

                $productQuoteObjectRefund = ProductQuoteObjectRefund::create(
                    $productQuoteRefund->pqr_id,
                    $flightQuoteTicketRefund->fqtr_id,
                    CurrencyHelper::convertToBaseCurrency($ticketForm->selling, $order->orClientCurrency->cur_base_rate),
                    CurrencyHelper::convertToBaseCurrency($ticketForm->airlinePenalty, $order->orClientCurrency->cur_base_rate),
                    CurrencyHelper::convertToBaseCurrency($ticketForm->processingFee, $order->orClientCurrency->cur_base_rate),
                    CurrencyHelper::convertToBaseCurrency($ticketForm->refundable, $order->orClientCurrency->cur_base_rate),
                    $form->getRefundForm()->currency,
                    $order->or_client_currency_rate,
                    $ticketForm->selling,
                    $ticketForm->airlinePenalty,
                    $ticketForm->processingFee,
                    $ticketForm->refundable,
                    null,
                    $ticketForm->toArray()
                );
                $productQuoteObjectRefund->new();
                $this->productQuoteObjectRefundRepository->save($productQuoteObjectRefund);
            }

            foreach ($form->getRefundForm()->auxiliaryOptionsForms as $auxiliaryOptionsForm) {
                $productQuoteOption = ProductQuoteOptionsQuery::getByProductQuoteIdOptionKey($originProductQuote->pq_id, $auxiliaryOptionsForm->type);

                $auxiliaryOptionsForm->details = Json::decode($auxiliaryOptionsForm->details);
                $auxiliaryOptionsForm->amountPerPax = Json::decode($auxiliaryOptionsForm->amountPerPax);
                $productQuoteOptionRefund = ProductQuoteOptionRefund::create(
                    $orderRefund->orr_id,
                    $productQuoteRefund->pqr_id,
                    $productQuoteOption->pqo_id ?? null,
                    CurrencyHelper::convertToBaseCurrency($auxiliaryOptionsForm->amount, $order->orClientCurrency->cur_base_rate),
                    null,
                    null,
                    CurrencyHelper::convertToBaseCurrency($auxiliaryOptionsForm->refundable, $order->orClientCurrency->cur_base_rate),
                    $form->getRefundForm()->currency,
                    $order->or_client_currency_rate,
                    $auxiliaryOptionsForm->amount,
                    $auxiliaryOptionsForm->refundable,
                    $auxiliaryOptionsForm->refundAllow,
                    $auxiliaryOptionsForm->toArray()
                );
                $productQuoteOptionRefund->new();
                $this->productQuoteOptionRefundRepository->save($productQuoteOptionRefund);
            }
        });
    }

    public function updateManual(ProductQuoteRefund $productQuoteRefund, VoluntaryRefundUpdateForm $form): void
    {
        $productQuoteRefund->pqr_client_selling_price = $form->totalPaid;
        $productQuoteRefund->pqr_client_refund_cost = $form->refundCost;
        $productQuoteRefund->pqr_client_processing_fee_amount = $form->totalProcessingFee;
        $productQuoteRefund->pqr_client_penalty_amount = $form->totalAirlinePenalty;
        $productQuoteRefund->pqr_client_refund_amount = $form->totalRefundable;
        $productQuoteRefund->calculateSystemPrices();

        $objects = [];
        foreach ($form->tickets as $ticketForm) {
            $productQuoteObjectRefund = $this->productQuoteObjectRefundRepository->find($ticketForm->id);

            $productQuoteObjectRefund->pqor_client_penalty_amount = $ticketForm->airlinePenalty;
            $productQuoteObjectRefund->pqor_client_processing_fee_amount = $ticketForm->processingFee;
            $productQuoteObjectRefund->pqor_client_refund_amount = $ticketForm->refundable;
            $productQuoteObjectRefund->pqor_client_selling_price = $ticketForm->selling;
            $productQuoteObjectRefund->calculateSystemPrices();
            $objects[] = $productQuoteObjectRefund;
        }

        $this->transactionManager->wrap(function () use ($productQuoteRefund, $objects) {
            $this->productQuoteRefundRepository->save($productQuoteRefund);
            foreach ($objects as $object) {
                $this->productQuoteObjectRefundRepository->save($object);
            }
        });
    }
}
