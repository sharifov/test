<?php

namespace modules\order\src\services;

use common\models\Payment;
use modules\invoice\src\entities\invoice\Invoice;
use modules\invoice\src\entities\invoice\InvoiceRepository;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderRepository;
use modules\order\src\entities\orderUserProfit\OrderUserProfit;
use modules\order\src\entities\orderUserProfit\OrderUserProfitRepository;
use modules\order\src\forms\api\PaymentForm;
use modules\order\src\forms\api\ProductQuotesForm;
use modules\order\src\payment\method\PaymentMethodRepository;
use modules\order\src\payment\PaymentRepository;
use modules\product\src\entities\productQuote\ProductQuote;
use sales\repositories\lead\LeadRepository;
use sales\repositories\product\ProductQuoteRepository;
use sales\services\RecalculateProfitAmountService;
use sales\services\TransactionManager;

/**
 * Class OrderApiManageService
 * @package modules\order\src\services
 *
 * @property OrderRepository $orderRepository
 * @property OrderUserProfitRepository $orderUserProfitRepository
 * @property TransactionManager $transactionManager
 * @property RecalculateProfitAmountService $recalculateProfitAmountService
 * @property ProductQuoteRepository $productQuoteRepository
 * @property InvoiceRepository $invoiceRepository
 * @property PaymentMethodRepository $paymentMethodRepository
 * @property PaymentRepository $paymentRepository
 * @property LeadRepository $leadRepository
 */
class OrderApiManageService
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var OrderUserProfitRepository
     */
    private $orderUserProfitRepository;
    /**
     * @var TransactionManager
     */
    private $transactionManager;
    /**
     * @var RecalculateProfitAmountService
     */
    private $recalculateProfitAmountService;
    /**
     * @var ProductQuoteRepository
     */
    private ProductQuoteRepository $productQuoteRepository;
    /**
     * @var InvoiceRepository
     */
    private InvoiceRepository $invoiceRepository;
    /**
     * @var PaymentMethodRepository
     */
    private PaymentMethodRepository $paymentMethodRepository;
    /**
     * @var PaymentRepository
     */
    private PaymentRepository $paymentRepository;
    /**
     * @var LeadRepository
     */
    private LeadRepository $leadRepository;

    public function __construct(
        OrderRepository $orderRepository,
        OrderUserProfitRepository $orderUserProfitRepository,
        RecalculateProfitAmountService $recalculateProfitAmountService,
        TransactionManager $transactionManager,
        ProductQuoteRepository $productQuoteRepository,
        InvoiceRepository $invoiceRepository,
        PaymentMethodRepository $paymentMethodRepository,
        PaymentRepository $paymentRepository,
        LeadRepository $leadRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderUserProfitRepository = $orderUserProfitRepository;
        $this->transactionManager = $transactionManager;
        $this->recalculateProfitAmountService = $recalculateProfitAmountService;
        $this->productQuoteRepository = $productQuoteRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->paymentRepository = $paymentRepository;
        $this->leadRepository = $leadRepository;
    }

    /**
     * @param CreateOrderDTO $dto
     * @param ProductQuotesForm[] $productQuotesForms
     * @param PaymentForm $paymentForm
     * @return Order
     * @throws \Throwable
     */
    public function createOrder(CreateOrderDTO $dto, array $productQuotesForms, PaymentForm $paymentForm): Order
    {
        return $this->transactionManager->wrap(function () use ($dto, $productQuotesForms, $paymentForm) {
            $newOrder = (new Order())->create($dto);
            $newOrder->processing();
            $orderId = $this->orderRepository->save($newOrder);
            $this->recalculateProfitAmountService->setOrders([$newOrder])->recalculateOrders();
            $lead = $newOrder->orLead;
            if ($lead) {
                $lead->booked();
                $this->leadRepository->save($lead);
            }

            $newOrderUserProfit = (new OrderUserProfit())->create($orderId, $newOrder->or_owner_user_id, 100, $newOrder->or_profit_amount);
            $this->orderUserProfitRepository->save($newOrderUserProfit);

            $totalOrderPrice = 0;
            foreach ($productQuotesForms as $productQuote) {
                $quote = ProductQuote::findByGid($productQuote->gid);
                $quote->setOrderRelation($newOrder->or_id);
                $quote->applied();
                $this->productQuoteRepository->save($quote);
                $totalOrderPrice += $quote->pq_price;

                foreach ($quote->productQuoteOptionsActive as $productQuoteOption) {
                    $totalOrderPrice += $productQuoteOption->pqo_price + $productQuoteOption->pqo_extra_markup;
                }
            }

            $invoice = Invoice::create(
                $newOrder->or_id,
                (float)$totalOrderPrice,
                $newOrder->or_client_currency,
                ''
            );
            $this->invoiceRepository->save($invoice);

            $paymentMethod = $this->paymentMethodRepository->findByKey($paymentForm->type);

            $payment = Payment::create(
                $paymentMethod->pm_id,
                $paymentForm->date,
                $paymentForm->amount,
                $paymentForm->currency,
                $invoice->inv_id,
                $newOrder->or_id,
                $paymentForm->transactionId
            );
            $payment->inProgress();
            $this->paymentRepository->save($payment);

            return $newOrder;
        });
    }
}
