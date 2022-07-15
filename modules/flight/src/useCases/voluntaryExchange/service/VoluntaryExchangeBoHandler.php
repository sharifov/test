<?php

namespace modules\flight\src\useCases\voluntaryExchange\service;

use common\components\hybrid\HybridWhData;
use common\components\purifier\Purifier;
use common\models\Notifications;
use common\models\Project;
use modules\flight\src\useCases\voluntaryExchangeCreate\service\VoluntaryExchangeCreateService;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use src\entities\cases\CaseEventLog;
use src\entities\cases\Cases;
use src\forms\cases\CasesChangeStatusForm;
use src\interfaces\BoWebhookService;
use webapi\src\forms\boWebhook\FlightVoluntaryExchangeUpdateForm;
use yii\base\Model;
use yii\db\Transaction;

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

    private ?FlightVoluntaryExchangeUpdateForm $form = null;
    private ?ProductQuote $originProductQuote = null;
    private ?ProductQuote $voluntaryQuote = null;
    private ?ProductQuoteChange $productQuoteChange = null;
    private ?Cases $case = null;
    private ?Project $project = null;

    /**
     * @param VoluntaryExchangeObjectCollection $voluntaryExchangeObjectCollection
     */
    public function __construct(
        VoluntaryExchangeObjectCollection $voluntaryExchangeObjectCollection
    ) {
        $this->objectCollection = $voluntaryExchangeObjectCollection;
    }

    /**
     * @throws \Throwable
     */
    public function processRequest(Model $form): void
    {
        if (!$form instanceof FlightVoluntaryExchangeUpdateForm) {
            throw new \RuntimeException('Form must be instanceof FlightVoluntaryExchangeUpdateForm');
        }

        $this->form = $form;
        if (!$this->originProductQuote = VoluntaryExchangeCreateService::getOriginProductQuote($this->form->booking_id, $this->form->gid)) {
            throw new \RuntimeException('OriginProductQuote not found by booking_id(' . $this->form->booking_id . ') or gid(' . $this->form->gid . ')');
        }

        $this->productQuoteChange = VoluntaryExchangeCreateService::getLastProductQuoteChangeByPqId(
            (int) $this->originProductQuote->pq_id,
            [ProductQuoteChangeStatus::IN_PROGRESS]
        );
        if (!$this->productQuoteChange) {
            throw new \RuntimeException('ProductQuoteChange not found by pqID(' . $this->originProductQuote->pq_id . ') bookingId(' . $this->form->booking_id . ')');
        }

        $this->voluntaryQuote = VoluntaryExchangeCreateService::getProductQuoteByProductQuoteChange(
            (int) $this->productQuoteChange->pqc_id,
            [ProductQuoteStatus::IN_PROGRESS]
        );
        if (!$this->voluntaryQuote) {
            throw new \RuntimeException('voluntaryQuote not found by pqcID(' . $this->productQuoteChange->pqc_id . ') bookingId(' . $this->form->booking_id . ')');
        }

        if (!$this->project = Project::findOne(['project_key' => $this->form->project_key])) {
            throw new \RuntimeException('Project not found by key(' . $this->form->project_key . ')');
        }
        $this->case = $this->productQuoteChange->pqcCase;

        switch ($form->status) {
            case FlightVoluntaryExchangeUpdateForm::STATUS_EXCHANGED:
                $this->handleExchanged();
                if ($this->case->project->getParams()->object->case->sendFeedback ?? null) {
                    $this->objectCollection
                        ->getCasesCommunicationService()
                        ->sendAutoFeedbackEmail($this->case, CaseEventLog::VOLUNTARY_EXCHANGE_WH_UPDATE)
                    ;
                }
                break;
            case FlightVoluntaryExchangeUpdateForm::STATUS_CANCELED:
                $this->handleCanceled();
                break;
            case FlightVoluntaryExchangeUpdateForm::STATUS_PENDING:
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
                'exchange_gid' => $this->productQuoteChange->pqc_gid,
                'exchange_status' => ProductQuoteChangeStatus::getClientKeyStatusById($this->productQuoteChange->pqc_status_id),
            ]
        )->getCollectedData();

        \Yii::info([
            'type' => HybridWhData::WH_TYPE_VOLUNTARY_CHANGE_UPDATE,
            'requestData' => $whData,
            'formBOData' => $form->toArray(),
            'ProductQuoteChangeStatus' => ProductQuoteChangeStatus::getName($this->productQuoteChange->pqc_status_id),
        ], 'info\Webhook::OTA::VoluntaryExchange:Request');

        $responseData = \Yii::$app->hybrid->wh(
            $this->project->id,
            HybridWhData::WH_TYPE_VOLUNTARY_CHANGE_UPDATE,
            ['data' => $whData]
        );

        \Yii::info([
            'type' => HybridWhData::WH_TYPE_VOLUNTARY_CHANGE_UPDATE,
            'responseData' => $responseData,
        ], 'info\Webhook::OTA::VoluntaryExchange:Response');
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

            if ($originFlightQuoteFlight = $this->originProductQuote->flightQuote->flightQuoteFlight ?? null) {
                $originFlightQuoteFlight->fqf_booking_id = null;
                $this->objectCollection->getFlightQuoteFlightRepository()->save($originFlightQuoteFlight);
            }

            $this->voluntaryQuote->bookedChangeFlow();
            $this->objectCollection->getProductQuoteRepository()->save($this->voluntaryQuote);

            if ($flightQuoteFlight = $this->voluntaryQuote->flightQuote->flightQuoteFlight ?? null) {
                $flightQuoteFlight->fqf_booking_id = $this->form->booking_id;
                $this->objectCollection->getFlightQuoteFlightRepository()->save($flightQuoteFlight);
            }

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
            $this->productQuoteChange->declined();
            $this->objectCollection->getProductQuoteChangeRepository()->save($this->productQuoteChange);

            $this->case->error(null, 'Exchanged from BackOffice request');
            $this->objectCollection->getCasesRepository()->save($this->case);

            $this->voluntaryQuote->declined();
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
