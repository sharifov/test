<?php

namespace modules\flight\src\useCases\voluntaryExchangeCreate\service;

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
use modules\flight\src\useCases\voluntaryExchangeCreate\form\VoluntaryExchangeCreateForm;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use sales\entities\cases\CaseEventLog;
use sales\entities\cases\Cases;
use sales\helpers\app\AppHelper;
use sales\helpers\ErrorsToStringHelper;
use webapi\src\services\payment\BillingInfoApiVoluntaryService;
use Yii;

/**
 * Class VoluntaryExchangeCreateHandler
 *
 * @property Cases $case;
 * @property FlightRequest $flightRequest
 * @property VoluntaryExchangeObjectCollection $objectCollection
 * @property VoluntaryExchangeService $voluntaryExchangeService
 * @property FlightRequestService $flightRequestService
 * @property CaseVoluntaryExchangeHandler $caseHandler
 *
 * @property ProductQuote $originProductQuote
 * @property ProductQuote $voluntaryExchangeQuote
 * @property ProductQuoteChange $productQuoteChange
 */
class VoluntaryExchangeCreateHandler
{
    private Cases $case;
    private FlightRequest $flightRequest;
    private VoluntaryExchangeObjectCollection $objectCollection;

    private VoluntaryExchangeService $voluntaryExchangeService;
    private FlightRequestService $flightRequestService;
    private CaseVoluntaryExchangeHandler $caseHandler;

    private ?ProductQuote $originProductQuote = null;
    private ?ProductQuote $voluntaryExchangeQuote = null;
    private ?ProductQuoteChange $productQuoteChange = null;

    /**
     * @param Cases $case
     * @param FlightRequest $flightRequest
     * @param VoluntaryExchangeObjectCollection $voluntaryExchangeObjectCollection
     */
    public function __construct(
        Cases $case,
        FlightRequest $flightRequest,
        VoluntaryExchangeObjectCollection $voluntaryExchangeObjectCollection
    ) {
        $this->case = $case;
        $this->flightRequest = $flightRequest;
        $this->objectCollection = $voluntaryExchangeObjectCollection;

        $this->voluntaryExchangeService =  new VoluntaryExchangeService($this->objectCollection);
        $this->flightRequestService = new FlightRequestService($flightRequest, $this->objectCollection);
        $this->caseHandler = new CaseVoluntaryExchangeHandler($case, $this->objectCollection);
    }

    public function processing(): void
    {
        $this->originProductQuote = VoluntaryExchangeCreateService::getOriginProductQuote($this->flightRequest->fr_booking_id);

        if ($this->originProductQuote && ($this->productQuoteChange = $this->originProductQuote->productQuoteLastChange)) {
            $this->voluntaryExchangeService->declineProductQuoteChange($this->productQuoteChange);
        }

        if (!$this->originProductQuote) {
            try {
                $saleData = $this->objectCollection
                    ->getBoRequestVoluntaryExchangeService()
                    ->getSaleData($this->flightRequest->fr_booking_id, $this->case, CaseEventLog::VOLUNTARY_EXCHANGE_CREATE);

                if ($caseSale = CaseSale::findOne(['css_cs_id' => $this->case->cs_id, 'css_sale_id' => $saleData['saleId']])) {
                    $caseSale->delete();
                }
                $this->voluntaryExchangeService->createCaseSale($saleData, $this->case);
            } catch (\Throwable $throwable) {
                $this->caseHandler->caseToPendingManual('Case sale not created');
                throw $throwable;
            }

            try {
                $client = $this->voluntaryExchangeService->getOrCreateClient(
                    $this->flightRequest->fr_project_id,
                    $this->objectCollection->getBoRequestVoluntaryExchangeService()->getOrderContactForm()
                );
                $this->caseHandler->addClient($client->id);
            } catch (\Throwable $throwable) {
                $this->caseHandler->caseToPendingManual('Client not created');
                throw $throwable;
            }

            try {
                $order = $this->voluntaryExchangeService->createOrder(
                    $this->objectCollection->getBoRequestVoluntaryExchangeService()->getOrderCreateFromSaleForm(),
                    $this->objectCollection->getBoRequestVoluntaryExchangeService()->getOrderContactForm(),
                    $this->case,
                    $this->flightRequest->fr_project_id
                );
            } catch (\Throwable $throwable) {
                $this->caseHandler->caseToPendingManual('Order not created');
                throw $throwable;
            }

            try {
                $this->originProductQuote = $this->voluntaryExchangeService->createOriginProductQuoteInfrastructure(
                    $this->objectCollection->getBoRequestVoluntaryExchangeService()->getOrderCreateFromSaleForm(),
                    $saleData,
                    $order,
                    $this->case
                );
            } catch (\Throwable $throwable) {
                $this->caseHandler->caseToPendingManual('OriginProductQuote not created');
                throw $throwable;
            }

            try {
                $this->caseHandler->setCaseDeadline($this->originProductQuote->flightQuote);
            } catch (\Throwable $throwable) {
                \Yii::warning(AppHelper::throwableLog($throwable), 'VoluntaryExchangeCreateJob:setCaseDeadline');
            }
        } else {
            $order = $this->originProductQuote->pqOrder;
        }

        try {
            if (empty($this->flightRequest->fr_data_json)) {
                throw new \RuntimeException('FlightRequest data_json cannot be empty');
            }
            $flightProductQuoteData = JsonHelper::decode($this->flightRequest->fr_data_json);
        } catch (\Throwable $throwable) {
            $this->caseHandler->caseToPendingManual('FlightRequest data_json is empty or corrupted');
            throw $throwable;
        }

        try {
            $this->productQuoteChange = $this->voluntaryExchangeService->createProductQuoteChange(
                $this->originProductQuote->pq_id,
                $this->case->cs_id,
                $flightProductQuoteData
            );
        } catch (\Throwable $throwable) {
            $this->caseHandler->caseToPendingManual('ProductQuoteChange not created');
            throw $throwable;
        }

        try {
            $this->voluntaryExchangeService->declineVoluntaryExchangeQuotes($this->originProductQuote, $this->case);
        } catch (\Throwable $throwable) {
            $this->caseHandler->caseToPendingManual('VoluntaryExchangeQuotes not declined');
            throw $throwable;
        }

        try {
            $flight = $this->voluntaryExchangeService->getFlightByOriginQuote($this->originProductQuote);
            $flightQuote = $this->objectCollection->getFlightQuoteManageService()->createVoluntaryExchange(
                $flight,
                $flightProductQuoteData,
                $this->originProductQuote->pq_order_id,
                $this->case,
                null,
                $this->originProductQuote
            );
            $this->caseHandler->setCaseDeadline($flightQuote);
            $this->voluntaryExchangeQuote = $flightQuote->fqProductQuote;
            $this->voluntaryExchangeService->addQuoteGidToDataJson($this->productQuoteChange, $this->voluntaryExchangeQuote->pq_gid);
            $this->voluntaryExchangeService->recommendedExchangeQuote($this->originProductQuote->pq_id, $this->voluntaryExchangeQuote->pq_id);
        } catch (\Throwable $throwable) {
            $this->caseHandler->caseToPendingManual('Could not create new Voluntary Exchange quote');
            throw $throwable;
        }

        try {
            $voluntaryExchangeCreateForm = new VoluntaryExchangeCreateForm();
            if (!$voluntaryExchangeCreateForm->load($flightProductQuoteData)) {
                throw new \RuntimeException('VoluntaryExchangeCreateForm not loaded');
            }
            if (!$voluntaryExchangeCreateForm->validate(['billing', 'payment_request'])) {
                throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($voluntaryExchangeCreateForm));
            }
        } catch (\Throwable $throwable) {
            $this->caseHandler->caseToPendingManual('FlightRequest json data is corrupted');
            throw $throwable;
        }

        if (
            !empty($voluntaryExchangeCreateForm->payment_request) &&
            $paymentRequestForm = $voluntaryExchangeCreateForm->getPaymentRequestForm()
        ) {
            try {
                $this->objectCollection->getPaymentRequestVoluntaryService()->processing(
                    $paymentRequestForm,
                    $order,
                    'Create by Voluntary Exchange API processing'
                );
            } catch (\Throwable $throwable) {
                $this->caseHandler->caseToPendingManual('PaymentRequest processing is failed');
                throw $throwable;
            }
        }

        if (
            !empty($voluntaryExchangeCreateForm->billing) &&
            ($billingInfoForm = $voluntaryExchangeCreateForm->getBillingInfoForm())
        ) {
            try {
                $paymentMethodId = $this->objectCollection->getPaymentRequestVoluntaryService()->getPaymentMethod()->pm_id ?? null;
                $creditCardId = $this->objectCollection->getPaymentRequestVoluntaryService()->getCreditCard()->cc_id ?? null;

                BillingInfoApiVoluntaryService::getOrCreateBillingInfo(
                    $billingInfoForm,
                    $order->getId(),
                    $creditCardId,
                    $paymentMethodId
                );
            } catch (\Throwable $throwable) {
                $this->caseHandler->caseToPendingManual('BillingInfo create is failed');
                throw $throwable;
            }
        }
    }

    public function doneProcess(): void
    {
        $this->case->awaiting(null, 'Voluntary Exchange api processing');
        $this->objectCollection->getCasesRepository()->save($this->case);

        $this->voluntaryExchangeQuote->inProgress(null, 'Voluntary Exchange api processing');
        $this->objectCollection->getProductQuoteRepository()->save($this->voluntaryExchangeQuote);

        $this->productQuoteChange->inProgress();
        $this->objectCollection->getProductQuoteChangeRepository()->save($this->productQuoteChange);

        $this->flightRequestService->done('FlightRequest successfully processed');

        if ($this->case->cs_user_id) {
            $linkToCase = Purifier::createCaseShortLink($this->case);
            Notifications::createAndPublish(
                $this->case->cs_user_id,
                'New VoluntaryExchange request',
                'New VoluntaryExchange request. Case: (' . $linkToCase . ')',
                Notifications::TYPE_INFO,
                true
            );
        }
        $this->case->addEventLog(
            CaseEventLog::VOLUNTARY_EXCHANGE_CREATE,
            'Voluntary Exchange process completed successfully'
        );
    }

    public function failProcess(string $description): void
    {
        if ($this->case) {
            $this->case->error(null, 'Voluntary Exchange api processing fail');
            if ($this->case->isAutomate()) {
                $this->case->offIsAutomate();
            }
            $this->objectCollection->getCasesRepository()->save($this->case);

            if ($this->case->cs_user_id) {
                $linkToCase = Purifier::createCaseShortLink($this->case);
                Notifications::createAndPublish(
                    $this->case->cs_user_id,
                    'New VoluntaryExchange request',
                    'Error in VoluntaryExchange request. Case: (' . $linkToCase . ')',
                    Notifications::TYPE_DANGER,
                    true
                );
            }
        }

        if ($this->productQuoteChange) {
            $this->productQuoteChange->error();
            $this->objectCollection->getProductQuoteChangeRepository()->save($this->productQuoteChange);
        }

        if ($this->flightRequestService) {
            $this->flightRequestService->error($description);
            if ($flightRequest = $this->flightRequestService->getFlightRequest()) {
                (new CleanDataVoluntaryExchangeService($flightRequest, $this->productQuoteChange, $this->objectCollection));
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
}
