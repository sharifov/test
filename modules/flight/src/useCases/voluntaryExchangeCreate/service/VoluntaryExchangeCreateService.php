<?php

namespace modules\flight\src\useCases\voluntaryExchangeCreate\service;

use common\models\CaseSale;
use common\models\Client;
use DomainException;
use modules\flight\models\Flight;
use modules\flight\src\useCases\sale\form\OrderContactForm;
use modules\flight\src\useCases\voluntaryExchange\service\VoluntaryExchangeObjectCollection;
use modules\flight\src\useCases\voluntaryExchangeCreate\form\VoluntaryExchangeCreateForm;
use modules\order\src\entities\order\Order;
use modules\order\src\services\createFromSale\OrderCreateFromSaleForm;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteData\ProductQuoteData;
use sales\entities\cases\CaseEventLog;
use sales\entities\cases\Cases;
use sales\forms\lead\EmailCreateForm;
use sales\forms\lead\PhoneCreateForm;
use sales\helpers\app\AppHelper;
use sales\helpers\ErrorsToStringHelper;
use sales\services\client\ClientCreateForm;
use Throwable;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class VoluntaryExchangeCreateService
 *
 * @property VoluntaryExchangeObjectCollection $objectCollection
 */
class VoluntaryExchangeCreateService
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
        return $originProductQuote;
    }

    public function recommendedExchangeQuote(int $originProductQuoteId, int $exchangeQuoteId): ProductQuoteData
    {
        return $this->objectCollection
            ->getProductQuoteDataManageService()
            ->updateRecommendedReprotectionQuote($originProductQuoteId, $exchangeQuoteId);
    }

    public function createProductQuoteChange(int $originProductQuoteId, int $caseId): ProductQuoteChange
    {
        $productQuoteChange = ProductQuoteChange::createVoluntaryExchange($originProductQuoteId, $caseId);
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

    public static function writeLog(Throwable $throwable, array $data = [], string $category = 'VoluntaryExchangeCreateJob:throwable'): void
    {
        $message = AppHelper::throwableLog($throwable);
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
