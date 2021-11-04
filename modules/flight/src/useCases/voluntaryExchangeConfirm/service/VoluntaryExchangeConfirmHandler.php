<?php

namespace modules\flight\src\useCases\voluntaryExchangeConfirm\service;

use common\components\purifier\Purifier;
use common\models\CaseSale;
use common\models\Notifications;
use frontend\helpers\JsonHelper;
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
use webapi\src\services\payment\BillingInfoApiVoluntaryService;
use Yii;

use function Amp\Promise\timeoutWithDefault;

/**
 * Class VoluntaryExchangeConfirmHandler
 *
 * @property Cases $case;
 * @property FlightRequest $flightRequest
 * @property VoluntaryExchangeObjectCollection $objectCollection
 * @property VoluntaryExchangeService $voluntaryExchangeService
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
    private Cases $case;
    private FlightRequest $flightRequest;
    private VoluntaryExchangeConfirmForm $confirmForm;
    private VoluntaryExchangeObjectCollection $objectCollection;

    private VoluntaryExchangeService $voluntaryExchangeService;
    private FlightRequestService $flightRequestService;
    private CaseVoluntaryExchangeHandler $caseHandler;

    private ?ProductQuote $originProductQuote = null;
    private ?ProductQuote $voluntaryExchangeQuote = null;
    private ?ProductQuoteChange $productQuoteChange = null;
    private ?Order $order = null;

    /**
     * @param Cases $case
     * @param FlightRequest $flightRequest
     * @param VoluntaryExchangeConfirmForm $confirmForm
     * @param VoluntaryExchangeObjectCollection $voluntaryExchangeObjectCollection
     */
    public function __construct(
        Cases $case,
        FlightRequest $flightRequest,
        VoluntaryExchangeConfirmForm $confirmForm,
        VoluntaryExchangeObjectCollection $voluntaryExchangeObjectCollection
    ) {
        /* TODO::  */
        $this->case = $case;
        $this->flightRequest = $flightRequest;
        $this->objectCollection = $voluntaryExchangeObjectCollection;
        $this->confirmForm = $confirmForm;

        $this->voluntaryExchangeService =  new VoluntaryExchangeService($this->objectCollection);
        $this->flightRequestService = new FlightRequestService($flightRequest, $this->objectCollection);
        $this->caseHandler = new CaseVoluntaryExchangeHandler($case, $this->objectCollection);
    }

    public function processing(): void
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

    public function doneProcess(): void
    {
        /* TODO::  */
    }

    public function failProcess(string $description): void
    {
        /* TODO::  */
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
}
