<?php

namespace modules\flight\src\useCases\voluntaryExchangeConfirm\service;

use common\components\purifier\Purifier;
use common\models\CaseSale;
use common\models\Notifications;
use frontend\helpers\JsonHelper;
use modules\flight\models\FlightQuote;
use modules\flight\models\FlightRequest;
use modules\flight\src\useCases\voluntaryExchange\service\CaseVoluntaryExchangeHandler;
use modules\flight\src\useCases\voluntaryExchange\service\CleanDataVoluntaryExchangeService;
use modules\flight\src\useCases\voluntaryExchange\service\FlightRequestService;
use modules\flight\src\useCases\voluntaryExchange\service\VoluntaryExchangeObjectCollection;
use modules\flight\src\useCases\voluntaryExchange\service\VoluntaryExchangeService;
use modules\flight\src\useCases\voluntaryExchangeConfirm\form\VoluntaryExchangeConfirmForm;
use modules\flight\src\useCases\voluntaryExchangeCreate\form\VoluntaryExchangeCreateForm;
use modules\order\src\entities\order\Order;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChangeRelation\ProductQuoteChangeRelation;
use modules\product\src\entities\productQuoteChangeRelation\ProductQuoteChangeRelationRepository;
use sales\entities\cases\CaseEventLog;
use sales\entities\cases\Cases;
use sales\helpers\app\AppHelper;
use sales\helpers\ErrorsToStringHelper;
use webapi\src\forms\billing\BillingInfoForm;
use webapi\src\forms\payment\PaymentRequestForm;
use webapi\src\services\payment\BillingInfoApiVoluntaryService;
use Yii;

use function Amp\Promise\timeoutWithDefault;

/**
 * Class VoluntaryExchangeConfirmHandler
 *
 * @property Cases $case;
 * @property FlightRequest $flightRequest
 * @property VoluntaryExchangeObjectCollection $objectCollection
 * @property FlightRequestService $flightRequestService
 * @property CaseVoluntaryExchangeHandler $caseHandler
 * @property VoluntaryExchangeConfirmForm $confirmForm
 *
 * @property ProductQuote $originProductQuote
 * @property ProductQuote $voluntaryExchangeQuote
 * @property ProductQuoteChange $productQuoteChange
 * @property Order $order
 */
class VoluntaryExchangeConfirmHandler
{

    private FlightRequest $flightRequest;
    private VoluntaryExchangeConfirmForm $confirmForm;
    private VoluntaryExchangeObjectCollection $objectCollection;

    private FlightRequestService $flightRequestService;
    private CaseVoluntaryExchangeHandler $caseHandler;

    private Cases $case;
    private ?ProductQuote $originProductQuote = null;
    private ?ProductQuote $voluntaryExchangeQuote = null;
    private ?ProductQuoteChange $productQuoteChange = null;
    private ?Order $order = null;

    /**
     * @param FlightRequest $flightRequest
     * @param VoluntaryExchangeConfirmForm $confirmForm
     * @param VoluntaryExchangeObjectCollection $voluntaryExchangeObjectCollection
     */
    public function __construct(
        FlightRequest $flightRequest,
        VoluntaryExchangeConfirmForm $confirmForm,
        VoluntaryExchangeObjectCollection $voluntaryExchangeObjectCollection
    ) {
        $this->confirmForm = $confirmForm;
        $this->flightRequest = $flightRequest;
        $this->objectCollection = $voluntaryExchangeObjectCollection;

        $this->case = $confirmForm->getCase();
        $this->originProductQuote = $confirmForm->getOriginQuote();
        $this->voluntaryExchangeQuote = $confirmForm->getOriginQuote();
        $this->productQuoteChange = $confirmForm->getProductQuoteChange();

        $this->flightRequestService = new FlightRequestService($flightRequest, $this->objectCollection);
        $this->caseHandler = new CaseVoluntaryExchangeHandler($this->case, $this->objectCollection);
    }

    public function prepareRequest(): void
    {
        $request['apiKey'] = $this->case->project->api_key;
        $request['bookingId'] = $this->confirmForm->booking_id;
        $request['billing'] = self::mappingBilling($this->confirmForm->getBillingInfoForm());
        $request['payment'] = self::mappingPayment($this->confirmForm->getPaymentRequestForm());

        /* TODO::  */
    }

    private function prepareExchange(): array
    {
        $data['currency'] = $this->voluntaryExchangeQuote->pq_client_currency ?: $this->voluntaryExchangeQuote->pq_origin_currency;
        $data['validatingCarrier'] = $this->voluntaryExchangeQuote->flightQuote->fq_main_airline;
        $data['gds'] = $this->voluntaryExchangeQuote->flightQuote->fq_gds;
        $data['pcc'] = $this->voluntaryExchangeQuote->flightQuote->fq_gds_pcc;
        $data['fareType'] = FlightQuote::getFareTypeNameById($this->voluntaryExchangeQuote->flightQuote->fq_fare_type_id);
        $data['cabin'] = $this->voluntaryExchangeQuote->flightQuote->fq_cabin_class;

        $caseSale = $this->getSale();

        /* TODO::  */

        //$this->originProductQuote->flightQuote->

        //$data['tickets'] = [];

        return $data;
    }

    private function getSale(): CaseSale
    {
        if (!$caseSale = CaseSale::findOne(['css_cs_id' => $this->case->cs_id, 'css_sale_book_id' => $this->confirmForm->booking_id])) {
            throw new \RuntimeException('CaseSale not found by case(' . $this->case->cs_id . ') and booking(' . $this->confirmForm->booking_id . ')');
        }
        return $caseSale;
    }

    public function doneProcess(): void
    {
        /* TODO::  */
    }

    public function failProcess(string $description): void
    {
        /* TODO::  */
    }

    public function additionalProcessing(): void
    {
        if (
            !empty($this->confirmForm->payment_request) &&
            $paymentRequestForm = $this->confirmForm->getPaymentRequestForm()
        ) {
            try {
                $this->objectCollection->getPaymentRequestVoluntaryService()->processing(
                    $paymentRequestForm,
                    $this->order,
                    'Create by Voluntary Exchange API processing'
                );
            } catch (\Throwable $throwable) {
                \Yii::warning(
                    AppHelper::throwableLog($throwable),
                    'VoluntaryExchangeCreateHandler:additionalProcessing:PaymentRequest'
                );
            }
        }

        if (
            !empty($this->confirmForm->billing) &&
            ($billingInfoForm = $this->confirmForm->getBillingInfoForm())
        ) {
            try {
                $paymentMethodId = $this->objectCollection->getPaymentRequestVoluntaryService()->getPaymentMethod()->pm_id ?? null;
                $creditCardId = $this->objectCollection->getPaymentRequestVoluntaryService()->getCreditCard()->cc_id ?? null;

                BillingInfoApiVoluntaryService::getOrCreateBillingInfo(
                    $billingInfoForm,
                    $this->order->getId() ?? null,
                    $creditCardId,
                    $paymentMethodId
                );
            } catch (\Throwable $throwable) {
                \Yii::warning(
                    AppHelper::throwableLog($throwable),
                    'VoluntaryExchangeCreateHandler:additionalProcessing:Billing'
                );
            }
        }
    }

    public function getOriginProductQuote(): ?ProductQuote
    {
        return $this->originProductQuote;
    }

    public function getVoluntaryExchangeQuote(): ?ProductQuote
    {
        return $this->voluntaryExchangeQuote;
    }

    public function getProductQuoteChange(): ?ProductQuoteChange
    {
        return $this->productQuoteChange;
    }

    public static function mappingBilling(?BillingInfoForm $billingInfoForm): array
    {
        $data['billing'] = null;
        if ($billingInfoForm) {
            $data['billing'] = [
                'address' => $billingInfoForm->address_line1,
                'countryCode' => $billingInfoForm->country_id,
                'country' => $billingInfoForm->country,
                'city' => $billingInfoForm->city,
                'state' => $billingInfoForm->state,
                'zip' => $billingInfoForm->zip,
                'phone' => $billingInfoForm->contact_phone,
                'email' => $billingInfoForm->contact_email
            ];
        }
        return $data;
    }
    public static function mappingPayment(?PaymentRequestForm $paymentRequestForm): array
    {
        $data['payment'] = null;
        if ($paymentRequestForm) {
            $data['payment'] = [
                'type' => mb_strtoupper($paymentRequestForm->method_key),
                'card' => [
                    'holderName' => $paymentRequestForm->creditCardForm->holder_name,
                    'number' => $paymentRequestForm->creditCardForm->number,
                    'expirationDate' => $paymentRequestForm->creditCardForm->expiration_month . '/' . $paymentRequestForm->creditCardForm->expiration_year,
                    'cvv' => $paymentRequestForm->creditCardForm->cvv
                ]
            ];
        }
        return $data;
    }
}
