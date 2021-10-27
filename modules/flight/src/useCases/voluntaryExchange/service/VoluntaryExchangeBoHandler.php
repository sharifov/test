<?php

namespace modules\flight\src\useCases\voluntaryExchange\service;

use common\components\hybrid\HybridWhData;
use common\components\purifier\Purifier;
use common\models\CaseSale;
use common\models\Client;
use common\models\Notifications;
use common\models\Project;
use DomainException;
use modules\flight\models\Flight;
use modules\flight\src\useCases\sale\form\OrderContactForm;
use modules\flight\src\useCases\voluntaryExchangeCreate\service\VoluntaryExchangeCreateService;
use modules\order\src\entities\order\Order;
use modules\order\src\services\createFromSale\OrderCreateFromSaleForm;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteQuery;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteData\ProductQuoteData;
use sales\entities\cases\CaseEventLog;
use sales\entities\cases\Cases;
use sales\forms\lead\EmailCreateForm;
use sales\forms\lead\PhoneCreateForm;
use sales\helpers\app\AppHelper;
use sales\helpers\ErrorsToStringHelper;
use sales\helpers\setting\SettingHelper;
use sales\interfaces\BoWebhookService;
use sales\services\client\ClientCreateForm;
use Throwable;
use webapi\src\forms\boWebhook\FlightVoluntaryExchangeUpdateForm;
use Yii;
use yii\base\Model;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;

/**
 * Class VoluntaryExchangeBoHandler
 *
 * @property VoluntaryExchangeObjectCollection $objectCollection
 *
 * @property FlightVoluntaryExchangeUpdateForm $form
 * @property ProductQuote $originProductQuote
 * @property ProductQuote $voluntaryQuote
 * @property ProductQuoteChange $productQuoteChange
 * @property Cases $case
 * @property Project $project
 */
class VoluntaryExchangeBoHandler implements BoWebhookService
{
    private VoluntaryExchangeObjectCollection $objectCollection;

    private ?FlightVoluntaryExchangeUpdateForm $form;
    private ?ProductQuote $originProductQuote;
    private ?ProductQuote $voluntaryQuote;
    private ?ProductQuoteChange $productQuoteChange;
    private ?Cases $case;
    private ?Project $project;

    /**
     * @param VoluntaryExchangeObjectCollection $voluntaryExchangeObjectCollection
     */
    public function __construct(
        VoluntaryExchangeObjectCollection $voluntaryExchangeObjectCollection
    ) {
        $this->objectCollection = $voluntaryExchangeObjectCollection;
    }

    public function processRequest(Model $form): void
    {
        if (!$form instanceof FlightVoluntaryExchangeUpdateForm) {
            throw new \RuntimeException('Form must be instanceof FlightVoluntaryExchangeUpdateForm');
        }
        $this->form = $form;
        if (!$this->originProductQuote = VoluntaryExchangeCreateService::getOriginProductQuote($this->form->booking_id)) {
            throw new \RuntimeException('OriginProductQuote not found by booking_id(' . $this->form->booking_id . ')');
        }
        if (!$this->productQuoteChange = VoluntaryExchangeCreateService::getLastProductQuoteChangeByPqId($this->originProductQuote->pq_id)) {
            throw new \RuntimeException('ProductQuoteChange not found by pqID(' . $this->originProductQuote->pq_id . ')');
        }
        if (!$this->voluntaryQuote = VoluntaryExchangeCreateService::getProductQuoteByProductQuoteChange($this->productQuoteChange->pqc_id)) {
            throw new \RuntimeException('voluntaryQuote not found by pqcID(' . $this->productQuoteChange->pqc_id . ')');
        }
        if (!$this->project = Project::findOne(['api_key' => $this->form->project_key])) {
            throw new \RuntimeException('Project not found by key(' . $this->form->project_key . ')');
        }
        $this->case = $this->productQuoteChange->pqcCase;

        switch ($form->status) {
            case FlightVoluntaryExchangeUpdateForm::STATUS_EXCHANGED:
                $this->handleExchanged();
                break;
            case FlightVoluntaryExchangeUpdateForm::STATUS_CANCELED:
                $this->handleCanceled();
                break;
            case FlightVoluntaryExchangeUpdateForm::STATUS_PROCESSING:
                $this->handleProcessing();
                break;
            default:
                throw new \RuntimeException('Unknown handler for status(' . $this->form->status . ')');
        }

        $this->case->addEventLog(
            CaseEventLog::VOLUNTARY_EXCHANGE_WH_UPDATE,
            'Exchanged from BackOffice processed. Status (' . $this->form->status . ')',
            ['status' => $this->form->status]
        );

        if ($this->case->cs_user_id) {
            $linkToCase = Purifier::createCaseShortLink($this->case);
            Notifications::createAndPublish(
                $this->case->cs_user_id,
                'Exchanged from BackOffice request processed',
                'Exchanged from BackOffice request processed. Status (' . $this->form->status . ') Case: (' . $linkToCase . ')',
                Notifications::TYPE_INFO,
                true
            );
        }

        $whData = (new HybridWhData())->fillCollectedData(
            HybridWhData::WH_TYPE_VOLUNTARY_CHANGE_UPDATE,
            [
                'booking_id' => $form->booking_id,
                'product_quote_gid' => $this->originProductQuote->pq_gid,
                'change_quote_gid' => $this->productQuoteChange->pqc_gid,
                'change_status_id' => $this->productQuoteChange->pqc_status_id,
            ]
        )->getCollectedData();
        \Yii::$app->hybrid->wh($this->project->id, HybridWhData::WH_TYPE_VOLUNTARY_REFUND_UPDATE, ['data' => $whData]);
    }

    private function handleExchanged(): void
    {
        $transaction = new Transaction(['db' => \Yii::$app->db]);
        try {
            $transaction->begin();
            $this->productQuoteChange->statusToComplete();
            $this->objectCollection->getProductQuoteChangeRepository()->save($this->productQuoteChange);

            $this->case->solved(null, 'Exchanged from BackOffice request');
            $this->objectCollection->getCasesRepository()->save($this->case);

            $this->originProductQuote->cancelled(null, 'Exchanged from BackOffice request');
            $this->objectCollection->getProductQuoteRepository()->save($this->originProductQuote);

            $this->voluntaryQuote->bookedChangeFlow();
            $this->objectCollection->getProductQuoteRepository()->save($this->voluntaryQuote);

            VoluntaryExchangeCreateService::bookingProductQuotePostProcessing($this->voluntaryQuote);
            $transaction->commit();
        } catch (\Throwable $throwable) {
            $transaction->rollBack();
            throw $throwable;
        }
    }

    private function handleCanceled(): void
    {
        $transaction = new Transaction(['db' => \Yii::$app->db]);
        try {
            $transaction->begin();
            $this->productQuoteChange->error();
            $this->objectCollection->getProductQuoteChangeRepository()->save($this->productQuoteChange);

            $this->case->error(null, 'Exchanged from BackOffice request');
            $this->objectCollection->getCasesRepository()->save($this->case);

            $this->voluntaryQuote->error();
            $this->objectCollection->getProductQuoteRepository()->save($this->voluntaryQuote);

            $transaction->commit();
        } catch (\Throwable $throwable) {
            $transaction->rollBack();
            throw $throwable;
        }
    }

    private function handleProcessing(): void
    {
    }
}
