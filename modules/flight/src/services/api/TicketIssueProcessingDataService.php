<?php

namespace modules\flight\src\services\api;

use common\models\Payment;
use common\models\PaymentMethod;
use modules\flight\models\FlightQuote;
use modules\flight\src\forms\api\FlightTicketIssueRequestApiForm;
use modules\flight\src\forms\api\FlightUpdateRequestApiForm;
use modules\flight\src\forms\api\TicketIssuePaymentApiForm;
use modules\flight\src\repositories\flightQuoteRepository\FlightQuoteRepository;
use modules\invoice\src\entities\invoice\Invoice;
use modules\order\src\entities\order\Order;
use modules\order\src\payment\PaymentRepository;
use sales\repositories\product\ProductQuoteRepository;
use sales\services\TransactionManager;
use modules\invoice\src\entities\invoice\InvoiceRepository;
use modules\order\src\payment\method\PaymentMethodRepository;

/**
 * Class TicketIssueProcessingDataService
 *
 * @property FlightQuoteRepository $flightQuoteRepository
 * @property ProductQuoteRepository $productQuoteRepository
 * @property TransactionManager $transactionManager
 * @property PaymentRepository $paymentRepository
 * @property InvoiceRepository $invoiceRepository
 * @property PaymentMethodRepository $paymentMethodRepository
 */
class TicketIssueProcessingDataService
{
    private FlightQuoteRepository $flightQuoteRepository;
    private ProductQuoteRepository $productQuoteRepository;
    private TransactionManager $transactionManager;
    private PaymentRepository $paymentRepository;
    private InvoiceRepository $invoiceRepository;
    private PaymentMethodRepository $paymentMethodRepository;

    /**
     * @param FlightQuoteRepository $flightQuoteRepository
     * @param ProductQuoteRepository $productQuoteRepository
     * @param TransactionManager $transactionManager
     * @param PaymentRepository $paymentRepository
     * @param InvoiceRepository $invoiceRepository
     * @param PaymentMethodRepository $paymentMethodRepository
     */
    public function __construct(
        FlightQuoteRepository $flightQuoteRepository,
        ProductQuoteRepository $productQuoteRepository,
        TransactionManager $transactionManager,
        PaymentRepository $paymentRepository,
        InvoiceRepository $invoiceRepository,
        PaymentMethodRepository $paymentMethodRepository
    ) {
        $this->flightQuoteRepository = $flightQuoteRepository;
        $this->productQuoteRepository = $productQuoteRepository;
        $this->transactionManager = $transactionManager;
        $this->paymentRepository = $paymentRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    public function processingQuote(FlightTicketIssueRequestApiForm $flightUpdateApiForm, array $post): void
    {
        foreach ($flightUpdateApiForm->flights as $key => $flight) {
            /** @var FlightQuote $flightQuote */
            $flightQuote = FlightQuote::findLastByFlightRequestUid($flight['uniqueId']);
            $flightQuote->fq_ticket_json = $post;
            $this->flightQuoteRepository->save($flightQuote);

            $productQuote = $flightQuote->fqProductQuote;
            $productQuote->booked();
            $this->productQuoteRepository->save($productQuote);
        }
    }

    public function processingPayment(FlightTicketIssueRequestApiForm $flightUpdateApiForm): void
    {
        $order = $flightUpdateApiForm->order;
        foreach ($flightUpdateApiForm->payments as $key => $payment) {
            $ticketIssuePaymentApiForm = new TicketIssuePaymentApiForm();
            $ticketIssuePaymentApiForm->load($payment);
            $ticketIssuePaymentApiForm->validate();

            $invoice = Invoice::create(
                $order->or_id,
                $ticketIssuePaymentApiForm->pay_amount,
                $ticketIssuePaymentApiForm->pay_currency,
                $ticketIssuePaymentApiForm->pay_description
            );
            $invoice->paid();
            $this->invoiceRepository->save($invoice);

            $methodId = null;
            if ($paymentMethod = PaymentMethod::findOne(['pm_key' => $key])) {
                $methodId = $paymentMethod->pm_id;
            }

            $payment = Payment::create(
                $methodId,
                $ticketIssuePaymentApiForm->pay_date,
                $ticketIssuePaymentApiForm->pay_amount,
                $ticketIssuePaymentApiForm->pay_currency,
                $invoice->inv_id,
                $order->or_id,
                $ticketIssuePaymentApiForm->pay_code
            );
            $payment->completed();
            $this->paymentRepository->save($payment);
        }
    }

    public function failQuote(Order $order): void
    {
        foreach ($order->productQuotes as $productQuote) {
            if ($productQuote->isFlight()) {
                $productQuote->error(null, 'From endpoint: /flight/update. Reason: flight_fail');
                $this->productQuoteRepository->save($productQuote);
            }
        }
    }
}
