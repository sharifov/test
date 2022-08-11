<?php

namespace modules\product\src\services;

use common\components\hybrid\HybridWhData;
use common\components\HybridService;
use common\models\Currency;
use modules\flight\models\FlightQuoteFlight;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteQuery;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeRepository;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use modules\flight\src\repositories\flightQuoteFlight\FlightQuoteFlightRepository;
use modules\product\src\entities\productQuoteChangeRelation\ProductQuoteChangeRelation;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use src\entities\cases\CaseEventLog;
use src\entities\cases\Cases;
use src\helpers\app\AppHelper;
use src\interfaces\BoWebhookService;
use src\repositories\cases\CasesRepository;
use src\services\cases\CasesCommunicationService;
use src\services\cases\CasesManageService;
use webapi\src\forms\boWebhook\ReprotectionUpdateForm;
use Yii;
use yii\base\Model;
use yii\db\Transaction;

/**
 * Class ProductQuoteService
 * @package modules\product\src\services
 *
 * @property ProductQuoteRepository $productQuoteRepository
 * @property FlightQuoteFlightRepository $flightQuoteFlightRepository
 * @property CasesManageService $casesManageService
 * @property CasesRepository $casesRepository
 * @property ProductQuoteChangeRepository$productQuoteChangeRepository
 * @property CasesCommunicationService $casesCommunicationService
 *
 * @property Cases $case
 * @property ProductQuoteChange $productQuoteChange
 * @property ProductQuote $productQuote
 * @property ReprotectionUpdateForm $form
 */
class ProductQuoteService implements BoWebhookService
{
    /**
     * @var ProductQuoteRepository
     */
    private ProductQuoteRepository $productQuoteRepository;
    /**
     * @var FlightQuoteFlightRepository
     */
    private FlightQuoteFlightRepository $flightQuoteFlightRepository;
    /**
     * @var CasesManageService
     */
    private CasesManageService $casesManageService;
    /**
     * @var CasesRepository
     */
    private CasesRepository $casesRepository;
    /**
     * @var ProductQuoteChangeRepository
     */
    private ProductQuoteChangeRepository $productQuoteChangeRepository;
    /**
     * @var CasesCommunicationService
     */
    private CasesCommunicationService $casesCommunicationService;
    /**
     * @var Cases|null
     */
    private ?Cases $case = null;
    /**
     * @var ProductQuoteChange|null
     */
    private ?ProductQuoteChange $productQuoteChange = null;
    /**
     * @var ProductQuote|null
     */
    private ?ProductQuote $productQuote = null;
    /**
     * @var ReprotectionUpdateForm|null
     */
    private ?ReprotectionUpdateForm $form = null;

    /**
     * ProductQuoteService constructor.
     * @param ProductQuoteRepository $productQuoteRepository
     * @param CasesManageService $casesManageService
     * @param CasesRepository $casesRepository
     * @param FlightQuoteFlightRepository $flightQuoteFlightRepository
     * @param ProductQuoteChangeRepository $productQuoteChangeRepository
     * @param CasesCommunicationService $casesCommunicationService
     */
    public function __construct(
        ProductQuoteRepository $productQuoteRepository,
        CasesManageService $casesManageService,
        CasesRepository $casesRepository,
        FlightQuoteFlightRepository $flightQuoteFlightRepository,
        ProductQuoteChangeRepository $productQuoteChangeRepository,
        CasesCommunicationService $casesCommunicationService
    ) {
        $this->productQuoteRepository = $productQuoteRepository;
        $this->casesManageService = $casesManageService;
        $this->casesRepository = $casesRepository;
        $this->flightQuoteFlightRepository = $flightQuoteFlightRepository;
        $this->productQuoteChangeRepository = $productQuoteChangeRepository;
        $this->casesCommunicationService = $casesCommunicationService;
    }

    /**
     * @param ProductQuote $productQuote
     * @param Currency $clientCurrency
     */
    public function recountProductQuoteClientPrice(ProductQuote $productQuote, Currency $clientCurrency): void
    {
        $productQuote->recountClientPrice($clientCurrency);
        $this->productQuoteRepository->save($productQuote);
    }

    public function detachProductQuoteFromOrder(ProductQuote $productQuote): void
    {
        if ($productQuote->isInProgress() || $productQuote->isPending()) {
            $productQuote->declined();
        }
        $productQuote->pq_order_id = null;
        $this->productQuoteRepository->save($productQuote);
    }

    /**
     * @param Model|ReprotectionUpdateForm $form
     * @return void
     */
    public function processRequest(Model $form): void
    {
        $this->form = $form;
        try {
            $this->productQuote = $this->productQuoteRepository
                ->findByGidFlightProductQuote($this->form->reprotection_quote_gid);
            if ($this->productQuote->isInProgress()) {
                if (!$this->productQuoteChange = $this->productQuote->productQuoteChangeLastRelation->pqcrPqc ?? null) {
                    throw new \RuntimeException('productQuoteChange not found');
                }
                $this->case = $this->productQuoteChange->pqcCase;
                if ($form->isCanceled()) {
                    $this->handleCanceled();
                } elseif ($form->isExchanged()) {
                    $this->handleExchanged();
                    if ($this->case->project->getParams()->object->case->sendFeedback ?? null) {
                        $this->casesCommunicationService
                            ->sendAutoFeedbackEmail($this->case, CaseEventLog::RE_PROTECTION_EXCHANGE)
                        ;
                    }
                } elseif ($form->isProcessing()) {
                    $this->productQuoteChange->statusToProcessing();
                    $this->productQuoteChangeRepository->save($this->productQuoteChange);
                }

                $this->case->addEventLog(
                    CaseEventLog::RE_PROTECTION_EXCHANGE,
                    sprintf('Exchanged from BackOffice processed. Status (%s)', $form->status),
                    ['status' => $form->status]
                );

                $this->hybridWh();
            }
        } catch (\Throwable $throwable) {
            $message = AppHelper::throwableLog($throwable);
            $message['data'] = $form->toArray();
            Yii::error(
                $message,
                'ProductQuoteService:processRequest:Throwable'
            );
        }
    }

    private function removeRelationsProcessing(): void
    {
        ProductQuoteRelation::deleteAll([
            'pqr_related_pq_id' => $this->productQuote->pq_id,
            'pqr_type_id' => ProductQuoteRelation::TYPE_REPROTECTION
        ]);
        ProductQuoteChangeRelation::deleteAll(['pqcr_pq_id' => $this->productQuote->pq_id]);
    }

    private function handleExchanged(): void
    {
        $flightOrigin = FlightQuoteFlight::find()
            ->andWhere([
                'fqf_booking_id' => $this->form->booking_id
            ])
            ->orderBy([
                'fqf_id' => SORT_DESC
            ])
            ->one();

        $flightReprotection = $this->productQuote->flightQuote->flightQuoteFlight;

        $transaction = new Transaction(['db' => \Yii::$app->db]);
        try {
            $transaction->begin();
            $this->productQuoteChange->statusToComplete();
            $this->productQuoteChangeRepository->save($this->productQuoteChange);
            $this->case->cs_is_automate = false;
            if ($this->case->isNeedAction()) {
                $this->case->offNeedAction();
            }
            $this->case->addEventLog(CaseEventLog::CASE_AUTO_PROCESSING_MARK, 'Case auto processing: disabled');
            $this->case->solved(null, 'Reprotection flight quote booked');
            $this->casesRepository->save($this->case);
            //why booking_id is rewritten
            if ($flightOrigin && $flightReprotection) {
                $flightReprotection->fqf_booking_id = $flightOrigin->fqf_booking_id;

                $flightOrigin->fqf_booking_id = null;
                $this->flightQuoteFlightRepository->save($flightOrigin);
                $this->flightQuoteFlightRepository->save($flightReprotection);
            }
            //=!
            $this->productQuote->booked();
            $this->productQuoteRepository->save($this->productQuote);
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
            $this->productQuote->declined();
            $this->productQuoteRepository->save($this->productQuote);
            $this->case->addEventLog(CaseEventLog::CASE_AUTO_PROCESSING_MARK, 'ProductQuote: declined');
            $this->productQuoteChange->statusToPending();
            $this->productQuoteChangeRepository->save($this->productQuoteChange);
            $this->case->addEventLog(CaseEventLog::CASE_AUTO_PROCESSING_MARK, 'ProductQuoteChange: pending');
            $this->case->cs_need_action = true;
            $this->case->error(null, 'Exchanged from BackOffice request');
            $this->casesRepository->save($this->case);
            $transaction->commit();
        } catch (\Throwable $throwable) {
            $transaction->rollBack();
            throw $throwable;
        }
    }

    private function hybridWh()
    {
        $originQuote = ProductQuoteQuery::getOriginProductQuoteByReprotection($this->productQuote->pq_id);
        $hybridService = Yii::createObject(HybridService::class);
        $whData = [
            'data' => [
                'booking_id' => $this->form->booking_id,
                'reprotection_quote_gid' => $this->productQuote->pq_gid,
                'case_gid' => $this->case->cs_gid,
                'product_quote_gid' => $originQuote->pq_gid ?? null,
                'status' => $this->form->isCanceled()
                    ? ProductQuoteChangeStatus::getClientKeyStatusById(ProductQuoteChangeStatus::DECLINED)
                    : ProductQuoteChangeStatus::getClientKeyStatusById($this->productQuoteChange->pqc_status_id),
            ]
        ];
        \Yii::info([
            'type' => HybridWhData::WH_TYPE_FLIGHT_SCHEDULE_CHANGE,
            'requestData' => $whData,
            'formBOData' => $this->form->toArray(),
            'ProductQuoteChangeStatus' => ProductQuoteChangeStatus::getName($this->productQuoteChange->pqc_status_id),
        ], 'info\Webhook::OTA::ScheduleChangeExchange:Request');
        $responseData = $hybridService->whReprotection($this->case->cs_project_id, $whData);
        \Yii::info([
            'type' => HybridWhData::WH_TYPE_FLIGHT_SCHEDULE_CHANGE,
            'responseData' => $responseData,
        ], 'info\Webhook::OTA::ScheduleChangeExchange:Response');
    }
}
