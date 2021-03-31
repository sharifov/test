<?php

namespace modules\order\src\services;

use common\models\BillingInfo;
use common\models\CreditCard;
use common\models\Payment;
use modules\flight\src\entities\flightQuoteOption\FlightQuoteOption;
use modules\flight\src\repositories\flightPaxRepository\FlightPaxRepository;
use modules\flight\src\repositories\flightQuoteOption\FlightQuoteOptionRepository;
use modules\invoice\src\entities\invoice\Invoice;
use modules\invoice\src\entities\invoice\InvoiceRepository;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderRepository;
use modules\order\src\entities\order\OrderStatusAction;
use modules\order\src\entities\orderTips\OrderTips;
use modules\order\src\entities\orderTips\OrderTipsRepository;
use modules\order\src\entities\orderUserProfit\OrderUserProfit;
use modules\order\src\entities\orderUserProfit\OrderUserProfitRepository;
use modules\order\src\forms\api\create\OrderCreateForm;
use modules\order\src\payment\method\PaymentMethodRepository;
use modules\order\src\payment\PaymentRepository;
use modules\product\src\entities\productHolder\ProductHolder;
use modules\product\src\entities\productHolder\ProductHolderRepository;
use modules\product\src\entities\productOption\ProductOptionRepository;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteOption\ProductQuoteOption;
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionRepository;
use sales\repositories\billingInfo\BillingInfoRepository;
use sales\repositories\creditCard\CreditCardRepository;
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
 * @property CreditCardRepository $creditCardRepository
 * @property BillingInfoRepository $billingInfoRepository
 * @property OrderTipsRepository $orderTipsRepository
 * @property ProductOptionRepository $productOptionRepository
 * @property ProductQuoteOptionRepository $productQuoteOptionRepository
 * @property FlightPaxRepository $flightPaxRepository
 * @property ProductHolderRepository $productHolderRepository
 * @property FlightQuoteOptionRepository $flightQuoteOptionRepository
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
    /**
     * @var CreditCardRepository
     */
    private CreditCardRepository $creditCardRepository;
    /**
     * @var BillingInfoRepository
     */
    private BillingInfoRepository $billingInfoRepository;
    /**
     * @var OrderTipsRepository
     */
    private OrderTipsRepository $orderTipsRepository;
    /**
     * @var ProductOptionRepository
     */
    private ProductOptionRepository $productOptionRepository;
    /**
     * @var ProductQuoteOptionRepository
     */
    private ProductQuoteOptionRepository $productQuoteOptionRepository;
    /**
     * @var FlightPaxRepository
     */
    private FlightPaxRepository $flightPaxRepository;
    /**
     * @var ProductHolderRepository
     */
    private ProductHolderRepository $productHolderRepository;
    /**
     * @var FlightQuoteOptionRepository
     */
    private FlightQuoteOptionRepository $flightQuoteOptionRepository;

    public function __construct(
        OrderRepository $orderRepository,
        OrderUserProfitRepository $orderUserProfitRepository,
        RecalculateProfitAmountService $recalculateProfitAmountService,
        TransactionManager $transactionManager,
        ProductQuoteRepository $productQuoteRepository,
        InvoiceRepository $invoiceRepository,
        PaymentMethodRepository $paymentMethodRepository,
        PaymentRepository $paymentRepository,
        LeadRepository $leadRepository,
        CreditCardRepository $creditCardRepository,
        BillingInfoRepository $billingInfoRepository,
        OrderTipsRepository $orderTipsRepository,
        ProductOptionRepository $productOptionRepository,
        ProductQuoteOptionRepository $productQuoteOptionRepository,
        FlightPaxRepository $flightPaxRepository,
        ProductHolderRepository $productHolderRepository,
        FlightQuoteOptionRepository $flightQuoteOptionRepository
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
        $this->creditCardRepository = $creditCardRepository;
        $this->billingInfoRepository = $billingInfoRepository;
        $this->orderTipsRepository = $orderTipsRepository;
        $this->productOptionRepository = $productOptionRepository;
        $this->productQuoteOptionRepository = $productQuoteOptionRepository;
        $this->flightPaxRepository = $flightPaxRepository;
        $this->productHolderRepository = $productHolderRepository;
        $this->flightQuoteOptionRepository = $flightQuoteOptionRepository;
    }

    /**
     * @param CreateOrderDTO $dto
     * @param OrderCreateForm $form
     * @return Order
     * @throws \Throwable
     */
    public function createOrder(CreateOrderDTO $dto, OrderCreateForm $form): Order
    {
        return $this->transactionManager->wrap(function () use ($dto, $form) {
            $newOrder = (new Order())->create($dto);
            $newOrder->processing(null, OrderStatusAction::API, null);
            $orderId = $this->orderRepository->save($newOrder);
//            $this->recalculateProfitAmountService->setOrders([$newOrder])->recalculateOrders();

            if ($newOrder->or_owner_user_id) {
                $newOrderUserProfit = (new OrderUserProfit())->create($orderId, $newOrder->or_owner_user_id, 100, $newOrder->or_profit_amount);
                $this->orderUserProfitRepository->save($newOrderUserProfit);
            }

            $totalOrderPrice = 0;
            foreach ($form->productQuotes as $productQuotesForm) {
                $quote = ProductQuote::findByGid($productQuotesForm->gid);
                if (!$quote->isNew() && !$quote->isPending()) {
                    throw new \DomainException('One of Quote(' . $productQuotesForm->gid . ') is not available status.');
                }
                $quote->setOrderRelation($newOrder->or_id);
                $quote->applied();
                $this->productQuoteRepository->save($quote);

                foreach ($productQuotesForm->productOptions as $quoteOptionsForm) {
                    $productOption = $this->productOptionRepository->findByKey($quoteOptionsForm->productOptionKey);

                    $productQuoteOption = ProductQuoteOption::create(
                        $quote->pq_id,
                        $productOption->po_id,
                        $quoteOptionsForm->name,
                        $quoteOptionsForm->description,
                        $quoteOptionsForm->price,
                        $quoteOptionsForm->price,
                        null,
                        $quoteOptionsForm->json_data
                    );
                    $productQuoteOption->calculateClientPrice();
                    $productQuoteOption->pending();
                    $this->productQuoteOptionRepository->save($productQuoteOption);

                    foreach ($quoteOptionsForm->data as $flightQuoteOptionForm) {
                        $flightQuoteOption = FlightQuoteOption::create(
                            $productQuoteOption->pqo_id,
                            $flightQuoteOptionForm->paxId,
                            $flightQuoteOptionForm->segmentId,
                            $flightQuoteOptionForm->tripId,
                            $flightQuoteOptionForm->display_name,
                            $flightQuoteOptionForm->markup_amount,
                            $flightQuoteOptionForm->usd_markup_amount,
                            $flightQuoteOptionForm->base_price,
                            $flightQuoteOptionForm->usd_base_price,
                            $flightQuoteOptionForm->total,
                            $flightQuoteOptionForm->usd_total,
                            $flightQuoteOptionForm->currency
                        );
                        $this->flightQuoteOptionRepository->save($flightQuoteOption);
                    }
                }

                $quote->recalculateProfitAmount();

                $totalOrderPrice += $quote->pq_price;
                foreach ($quote->productQuoteOptions as $productQuoteOption) {
                    $totalOrderPrice += $productQuoteOption->pqo_price + $productQuoteOption->pqo_extra_markup;
                }

                $productHolder = ProductHolder::create(
                    $quote->pq_product_id,
                    $productQuotesForm->productHolder->firstName,
                    $productQuotesForm->productHolder->lastName,
                    $productQuotesForm->productHolder->email,
                    $productQuotesForm->productHolder->phone,
                );
                $this->productHolderRepository->save($productHolder);
            }

            $orderAmount = 0;
            if ($form->tips->total_amount) {
                $orderTips = new OrderTips();
                $orderTips->ot_order_id = $newOrder->or_id;
                $orderTips->ot_client_amount = $form->tips->total_amount;
                $orderTips->ot_amount = $form->tips->total_amount;
                $this->orderTipsRepository->save($orderTips);
                $orderAmount = $orderTips->ot_amount;
            }

            $invoice = Invoice::create(
                $newOrder->or_id,
                (float)$totalOrderPrice + $orderAmount,
                $newOrder->or_client_currency,
                ''
            );
            $this->invoiceRepository->save($invoice);

            $paymentMethod = $this->paymentMethodRepository->findByKey($form->payment->type);

            $payment = Payment::create(
                $paymentMethod->pm_id,
                $form->payment->date,
                $form->payment->amount,
                $form->payment->currency,
                $invoice->inv_id,
                $newOrder->or_id,
                $form->payment->transactionId
            );
            $payment->inProgress();
            $this->paymentRepository->save($payment);

            $creditCard = CreditCard::create(
                $form->creditCard->number,
                $form->creditCard->holder_name,
                $form->creditCard->expiration_month,
                $form->creditCard->expiration_year,
                $form->creditCard->cvv,
                $form->creditCard->type_id,
            );
            $creditCard->updateSecureCardNumber();
            $creditCard->updateSecureCvv();
            $this->creditCardRepository->save($creditCard);

            $billingInfo = BillingInfo::create(
                $form->billingInfo->first_name,
                $form->billingInfo->last_name,
                $form->billingInfo->middle_name,
                $form->billingInfo->address,
                $form->billingInfo->city,
                $form->billingInfo->state,
                $form->billingInfo->country_id,
                $form->billingInfo->zip,
                $form->billingInfo->phone,
                $form->billingInfo->email,
                $paymentMethod->pm_id,
                $creditCard->cc_id,
                $newOrder->or_id
            );
            $this->billingInfoRepository->save($billingInfo);

            $lead = $newOrder->orLead;
            if ($lead) {
                $lead->booked($newOrder->or_owner_user_id);
                $this->leadRepository->save($lead);
            }

            foreach ($form->paxes as $paxForm) {
                $pax = $this->flightPaxRepository->findByUid($paxForm->uid);
                $pax->updateByOrderApiCreation(
                    $paxForm->first_name,
                    $paxForm->last_name,
                    $paxForm->middle_name,
                    $paxForm->nationality,
                    $paxForm->gender,
                    $paxForm->birth_date,
                    $paxForm->email,
                    $paxForm->language,
                    $paxForm->citizenship
                );
                $this->flightPaxRepository->save($pax);
            }

            return $newOrder;
        });
    }

    public function createByC2bFlow(CreateOrderDTO $dto): Order
    {
        $newOrder = (new Order())->create($dto);
        $newOrder->processing(null, OrderStatusAction::API, null);
        $this->orderRepository->save($newOrder);

        return $newOrder;
    }
}
