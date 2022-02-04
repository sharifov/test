<?php

namespace modules\flight\src\useCases\voluntaryExchange\service;

use common\components\purifier\Purifier;
use common\models\CaseSale;
use common\models\Client;
use common\models\Notifications;
use DomainException;
use modules\flight\models\Flight;
use modules\flight\src\useCases\sale\form\OrderContactForm;
use modules\order\src\entities\order\Order;
use modules\order\src\services\createFromSale\OrderCreateFromSaleForm;
use modules\order\src\services\OrderManageService;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteQuery;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteData\ProductQuoteData;
use src\entities\cases\CaseEventLog;
use src\entities\cases\Cases;
use src\forms\lead\EmailCreateForm;
use src\forms\lead\PhoneCreateForm;
use src\helpers\app\AppHelper;
use src\helpers\ErrorsToStringHelper;
use src\helpers\setting\SettingHelper;
use src\services\client\ClientCreateForm;
use Throwable;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class VoluntaryExchangeService
 *
 * @property VoluntaryExchangeObjectCollection $objectCollection
 */
class VoluntaryExchangeService
{
    private VoluntaryExchangeObjectCollection $objectCollection;

    /**
     * @param VoluntaryExchangeObjectCollection $voluntaryExchangeObjectCollection
     */
    public function __construct(
        VoluntaryExchangeObjectCollection $voluntaryExchangeObjectCollection
    ) {
        $this->objectCollection = $voluntaryExchangeObjectCollection;
    }

    public function originProductQuoteDecline(ProductQuote $originProductQuote, Cases $case): ProductQuote
    {
        if (!$originProductQuote->isDeclined()) {
            $originProductQuote->declined();
            $this->objectCollection->getProductQuoteRepository()->save($originProductQuote);
            $case->addEventLog(
                CaseEventLog::VOLUNTARY_EXCHANGE_CREATE,
                'Origin ProductQuote declined',
                ['pq_gid' => $originProductQuote->pq_gid]
            );
        }
        return $originProductQuote;
    }

    public function createOrder(
        OrderCreateFromSaleForm $orderCreateFromSaleForm,
        OrderContactForm $orderContactForm,
        Cases $case,
        int $projectId
    ): Order {
        $bookingId = !empty($orderCreateFromSaleForm->baseBookingId) ? $orderCreateFromSaleForm->baseBookingId : $orderCreateFromSaleForm->bookingId;
        if ($order = OrderManageService::getBySaleIdOrBookingId($orderCreateFromSaleForm->saleId, $bookingId)) {
            $this->objectCollection->getOrderCreateFromSaleService()->caseOrderRelation($order->getId(), $case->cs_id);
            $case->addEventLog(
                CaseEventLog::VOLUNTARY_EXCHANGE_CREATE,
                'Order linked. Gid:' . $order->or_gid,
                ['order_gid' => $order->or_gid]
            );
            return $order;
        }

        $order = $this->objectCollection->getOrderCreateFromSaleService()->orderCreate($orderCreateFromSaleForm);
        if (!$order->validate()) {
            throw new DomainException(ErrorsToStringHelper::extractFromModel($order));
        }
        $order->or_project_id = $projectId;
        $orderId = $this->objectCollection->getOrderRepository()->save($order);

        $this->objectCollection->getOrderCreateFromSaleService()->caseOrderRelation($orderId, $case->cs_id);
        $this->objectCollection->getOrderCreateFromSaleService()->orderContactCreate($order, $orderContactForm);

        $case->addEventLog(
            CaseEventLog::VOLUNTARY_EXCHANGE_CREATE,
            'Order created GID: ' . $order->or_gid,
            ['order_gid' => $order->or_gid]
        );

        return $order;
    }

    public function createOriginProductQuoteInfrastructure(
        OrderCreateFromSaleForm $orderCreateFromSaleForm,
        array $saleData,
        Order $order,
        Cases $case
    ): ProductQuote {
        $originProductQuote = $this->objectCollection->getFlightFromSaleService()->createHandler($order, $orderCreateFromSaleForm, $saleData);
        $case->addEventLog(
            CaseEventLog::VOLUNTARY_EXCHANGE_CREATE,
            'Origin ProductQuote created GID: ' . $originProductQuote->pq_gid,
            ['pq_gid' => $originProductQuote->pq_gid]
        );
        $originProductQuote->bookedChangeFlow();
        $this->objectCollection->getProductQuoteRepository()->save($originProductQuote);
        return $originProductQuote;
    }

    public function recommendedExchangeQuote(int $originProductQuoteId, int $exchangeQuoteId): ProductQuoteData
    {
        return $this->objectCollection
            ->getProductQuoteDataManageService()
            ->updateRecommendedChangeQuote($originProductQuoteId, $exchangeQuoteId);
    }

    public function createProductQuoteChange(int $originProductQuoteId, int $caseId, array $dataJson): ProductQuoteChange
    {
        $productQuoteChange = ProductQuoteChange::createVoluntaryExchange($originProductQuoteId, $caseId);
        $productQuoteChange->setDataJson($dataJson);
        $productQuoteChange->statusToPending();
        $this->objectCollection->getProductQuoteChangeRepository()->save($productQuoteChange);
        return $productQuoteChange;
    }

    public function addQuoteGidToDataJson(ProductQuoteChange $productQuoteChange, string $exchangeQuoteGid): ProductQuoteChange
    {
        $dataJson = $productQuoteChange->pqc_data_json;
        $dataJson['quote_gid'] = $exchangeQuoteGid;
        $productQuoteChange->pqc_data_json = $dataJson;
        $this->objectCollection->getProductQuoteChangeRepository()->save($productQuoteChange);
        return $productQuoteChange;
    }

    public function getFlightByOriginQuote(ProductQuote $originProductQuote): Flight
    {
        if ($flight = $originProductQuote->flightQuote->fqFlight ?? null) {
            return $flight;
        }
        throw new DomainException('Flight by OriginQuote not found');
    }

    public function createCaseSale(array $saleData, Cases $case): ?CaseSale
    {
        $caseSale = $this->objectCollection->getCasesSaleService()->createSaleByData($case->cs_id, $saleData);
        $case->addEventLog(
            CaseEventLog::VOLUNTARY_EXCHANGE_CREATE,
            'Case Sale created by Data',
            ['case_id' => $case->cs_id]
        );
        return $caseSale;
    }

    public function createSimpleClient(int $projectId): Client
    {
        $clientForm = ClientCreateForm::createWidthDefaultName();
        $clientForm->projectId = $projectId;
        $clientForm->typeCreate = Client::TYPE_CREATE_CASE;
        return $this->objectCollection->getClientManageService()->create($clientForm, null);
    }

    public function getOrCreateClient(int $projectId, OrderContactForm $orderContactForm): Client
    {
        $clientForm = new ClientCreateForm();
        $clientForm->projectId = $projectId;
        $clientForm->typeCreate = Client::TYPE_CREATE_CASE;
        $clientForm->firstName = $orderContactForm->first_name;
        $clientForm->lastName = $orderContactForm->last_name;

        return $this->objectCollection->getClientManageService()->getOrCreate(
            [new PhoneCreateForm(['phone' => $orderContactForm->phone_number])],
            [new EmailCreateForm(['email' => $orderContactForm->email])],
            $clientForm
        );
    }

    public function declineProductQuoteChange(ProductQuoteChange $productQuoteChange): ProductQuoteChange
    {
        $finishedQuoteChangeStatuses = array_keys(SettingHelper::getFinishedQuoteChangeStatuses());
        if (!in_array($productQuoteChange->pqc_status_id, $finishedQuoteChangeStatuses, false)) {
            $productQuoteChange->declined();
            $this->objectCollection->getProductQuoteChangeRepository()->save($productQuoteChange);
        }
        return $productQuoteChange;
    }

    public function declineVoluntaryExchangeQuotes(ProductQuote $originProductQuote, Cases $case, ?int $userId = null): array
    {
        $declinedIds = [];
        if ($voluntaryQuotes = ProductQuoteQuery::getVoluntaryExchangeQuotesByOriginQuote($originProductQuote->pq_id)) {
            foreach ($voluntaryQuotes as $voluntaryExchangeQuote) {
                if (!$voluntaryExchangeQuote->isDeclined()) {
                    $voluntaryExchangeQuote->declined($userId, 'Declined from Voluntary Exchange process');
                    $this->objectCollection->getProductQuoteRepository()->save($voluntaryExchangeQuote);
                    $declinedIds[] = $voluntaryExchangeQuote->pq_id;
                }
            }
            if ($declinedIds) {
                $case->addEventLog(
                    CaseEventLog::VOLUNTARY_EXCHANGE_CREATE,
                    'Old Voluntary Exchange Quotes declined',
                    ['originProductQuoteGid' => $originProductQuote->pq_gid]
                );
            }
        }
        return $declinedIds;
    }

    public function doneProcess(
        ProductQuote $voluntaryExchangeQuote,
        Cases $case,
        ProductQuoteChange $productQuoteChange,
        FlightRequestService $flightRequestService
    ): void {
        $case->awaiting(null, 'Voluntary Exchange api processing');
        $this->objectCollection->getCasesRepository()->save($case);

        $voluntaryExchangeQuote->inProgress(null, 'Voluntary Exchange api processing');
        $this->objectCollection->getProductQuoteRepository()->save($voluntaryExchangeQuote);

        $productQuoteChange->inProgress();
        $this->objectCollection->getProductQuoteChangeRepository()->save($productQuoteChange);

        $flightRequestService->done('FlightRequest successfully processed');

        if ($case->cs_user_id) {
            $linkToCase = Purifier::createCaseShortLink($case);
            Notifications::createAndPublish(
                $case->cs_user_id,
                'New VoluntaryExchange request',
                'New VoluntaryExchange request. Case: (' . $linkToCase . ')',
                Notifications::TYPE_INFO,
                true
            );
        }
        $case->addEventLog(
            CaseEventLog::VOLUNTARY_EXCHANGE_CREATE,
            'Voluntary Exchange process completed successfully'
        );
    }

    public function failProcess(
        string $description,
        ?Cases $case,
        ?ProductQuoteChange $productQuoteChange,
        ?FlightRequestService $flightRequestService
    ): void {
        if ($case) {
            $case->error(null, 'Voluntary Exchange api processing fail');
            if ($case->isAutomate()) {
                $case->offIsAutomate();
            }
            $this->objectCollection->getCasesRepository()->save($case);

            if ($case->cs_user_id) {
                $linkToCase = Purifier::createCaseShortLink($case);
                Notifications::createAndPublish(
                    $case->cs_user_id,
                    'New VoluntaryExchange request',
                    'Error in VoluntaryExchange request. Case: (' . $linkToCase . ')',
                    Notifications::TYPE_DANGER,
                    true
                );
            }
        }

        if ($productQuoteChange) {
            $productQuoteChange->error();
            $this->objectCollection->getProductQuoteChangeRepository()->save($productQuoteChange);
        }

        if ($flightRequestService) {
            $flightRequestService->error($description);

            if ($flightRequest = $flightRequestService->getFlightRequest()) {
                (new CleanDataVoluntaryExchangeService($flightRequest, $productQuoteChange, $this->objectCollection));
            }
        }
    }

    public static function writeLog(Throwable $throwable, string $category, array $data = []): void
    {
        $message = AppHelper::throwableLog($throwable, YII_DEBUG);
        if ($data) {
            $message = ArrayHelper::merge($message, $data);
        }
        if ($throwable instanceof DomainException) {
            Yii::warning($message, $category);
        } else {
            Yii::error($message, $category);
        }
    }
}
