<?php

namespace common\components\jobs;

use common\components\HybridService;
use common\components\purifier\Purifier;
use common\models\CaseNote;
use common\models\CaseSale;
use common\models\ClientEmail;
use common\models\Notifications;
use DomainException;
use modules\flight\models\FlightRequest;
use modules\flight\src\useCases\flightQuote\FlightQuoteManageService;
use modules\flight\src\useCases\reprotectionCreate\service\BoRequestReProtectionService;
use modules\flight\src\useCases\reprotectionCreate\service\CaseReProtectionService;
use modules\flight\src\useCases\reprotectionCreate\service\FlightRequestService;
use modules\flight\src\useCases\reprotectionCreate\service\OtaRequestReProtectionService;
use modules\flight\src\useCases\reprotectionCreate\service\ReprotectionCreateService;
use modules\flight\src\useCases\reprotectionCreate\service\SendEmailReProtectionService;
use modules\flight\src\useCases\sale\form\OrderContactForm;
use modules\order\src\services\createFromSale\OrderCreateFromSaleForm;
use modules\order\src\services\createFromSale\OrderCreateFromSaleService;
use modules\product\src\entities\productQuote\ProductQuoteQuery;
use modules\product\src\entities\productQuoteChange\events\ProductQuoteChangeAutoDecisionPendingEvent;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeQuery;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeRepository;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use modules\product\src\entities\productQuoteChangeRelation\ProductQuoteChangeRelation;
use modules\product\src\entities\productQuoteChangeRelation\ProductQuoteChangeRelationQueryScopes;
use modules\product\src\entities\productQuoteChangeRelation\ProductQuoteChangeRelationRepository;
use modules\product\src\entities\productQuoteChangeRelation\service\ProductQuoteChangeRelationService;
use modules\product\src\entities\productQuoteData\service\ProductQuoteDataManageService;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use modules\product\src\repositories\ProductQuoteRelationRepository;
use src\dispatchers\EventDispatcher;
use src\entities\cases\CaseCategory;
use src\entities\cases\CaseEventLog;
use src\entities\cases\Cases;
use src\entities\cases\CasesStatus;
use src\exception\BoResponseException;
use src\exception\CheckRestrictionException;
use src\exception\ValidationException;
use src\helpers\app\AppHelper;
use src\helpers\ErrorsToStringHelper;
use src\helpers\setting\SettingHelper;
use src\model\caseOrder\entity\CaseOrder;
use src\model\caseOrder\entity\CaseOrderQuery;
use src\services\cases\CasesCommunicationService;
use src\services\cases\CasesSaleService;
use src\services\email\SendEmailByCase;
use Throwable;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;
use yii\queue\Queue;
use src\repositories\product\ProductQuoteRepository;

/**
 * @property int $flight_request_id
 * @property bool $flight_request_is_automate
 */
class ReprotectionCreateJob extends BaseJob implements JobInterface
{
    public $flight_request_id;
    public $flight_request_is_automate;

    /**
     * @param Queue $queue
     * @return bool
     */
    public function execute($queue): void
    {
        $this->waitingTimeRegister();

        $reProtectionCreateService = Yii::createObject(ReprotectionCreateService::class);
        $flightQuoteManageService = Yii::createObject(FlightQuoteManageService::class);
        $caseReProtectionService = Yii::createObject(CaseReProtectionService::class);
        $boRequestReProtectionService = Yii::createObject(BoRequestReProtectionService::class);
        $sendEmailReProtectionService = Yii::createObject(SendEmailReProtectionService::class);
        $productQuoteChangeRepository = Yii::createObject(ProductQuoteChangeRepository::class);
        $flightRequestService = Yii::createObject(FlightRequestService::class);
        $eventDispatcher = Yii::createObject(EventDispatcher::class);
        $productQuoteDataManageService = Yii::createObject(ProductQuoteDataManageService::class);

        $client = null;

        try {
            if (!$flightRequest = FlightRequest::findOne($this->flight_request_id)) {
                throw new DomainException('FlightRequest not found, ID (' . $this->flight_request_id . ')');
            }
            $flightRequestService->setFlightRequest($flightRequest);

            $originProductQuote = ProductQuoteQuery::getProductQuoteByBookingId($flightRequest->fr_booking_id);

            if ($originProductQuote && $reProtectionCreateService::isChangeExist($originProductQuote)) {
                if ($lastCase = $caseReProtectionService::getLastCaseByBookingId($flightRequest->fr_booking_id, null)) {
                    $lastCase->addEventLog(
                        CaseEventLog::RE_PROTECTION_CREATE,
                        'ReProtection request is declined. Change processing already exist',
                        ['booking_id' => $flightRequest->fr_booking_id],
                        CaseEventLog::CATEGORY_WARNING
                    );
                }
                $flightRequestService->error('Reason: Change processing already exist. Origin Product Quote(' .
                    $originProductQuote->pq_gid . ')');
                return;
            }

            if ($originProductQuote && $reProtectionCreateService::isScheduleChangeActiveExist($originProductQuote)) {
                $flightRequestService->error('Reason: Product Quote Schedule Change Active exist. Origin Product Quote(' .
                    $originProductQuote->pq_gid . ')');
                return;
            }

            if (!$case = $caseReProtectionService::getLastActiveCaseByBookingId($flightRequest->fr_booking_id)) {
                $case = $caseReProtectionService->createCase($flightRequest);

                if ($clientId = $originProductQuote->productQuoteLastChange->pqcCase->cs_client_id ?? null) {
                    $caseReProtectionService->additionalFillingCase($clientId, $flightRequest->fr_project_id);
                }
            }
            $caseReProtectionService->setCase($case);

            if ($originProductQuote && $updatableScheduleChanges = $reProtectionCreateService::getUpdatableScheduleChanges($originProductQuote)) {
                foreach ($updatableScheduleChanges as $oldScheduleChange) {
                    $oldScheduleChange->declined();
                    $productQuoteChangeRepository->save($oldScheduleChange);
                }

                $productQuoteChange = ProductQuoteChange::createReProtection(
                    $originProductQuote->pq_id,
                    $case->cs_id,
                    $this->flight_request_is_automate,
                    $flightRequestService->getIsRefundAllowed()
                );
                $productQuoteChangeRepository->save($productQuoteChange);

                $case->addEventLog(
                    CaseEventLog::RE_PROTECTION_CREATE,
                    'New schedule change happened',
                    ['change' => $productQuoteChange->pqc_gid],
                    CaseEventLog::CATEGORY_INFO
                );
            }

            if (!$originProductQuote || !$reProtectionCreateService::isScheduleChangeUpdatableExist($originProductQuote)) {
                try {
                    $saleData = $boRequestReProtectionService->getSaleData($flightRequest->fr_booking_id, $case);

                    $client = $reProtectionCreateService->getOrCreateClient(
                        $flightRequest->fr_project_id,
                        $boRequestReProtectionService->getOrderContactForm()
                    );

                    $caseReProtectionService->additionalFillingCase($client->id, $flightRequest->fr_project_id);
                    if ($caseSale = CaseSale::findOne(['css_cs_id' => $case->cs_id, 'css_sale_id' => $saleData['saleId']])) {
                        $caseSale->delete();
                    }
                    $reProtectionCreateService->createCaseSale($saleData, $case);
                } catch (Throwable $throwable) {
                    $case->addEventLog(CaseEventLog::RE_PROTECTION_CREATE, 'Case sale not created');
                    $caseReProtectionService->caseToManual('Case sale not created');
                    if (isset($flightRequest) && $client === null) {
                        $client = $reProtectionCreateService->createSimpleClient($flightRequest->fr_project_id);
                        $caseReProtectionService->additionalFillingCase($client->id, $flightRequest->fr_project_id);
                    }
                    $flightRequestService->error(VarDumper::dumpAsString($throwable->getMessage()));
                    $reProtectionCreateService::writeLog($throwable);
                    return;
                }

                if (!$originProductQuote) {
                    try {
                        $order = $reProtectionCreateService->createOrder(
                            $boRequestReProtectionService->getOrderCreateFromSaleForm(),
                            $boRequestReProtectionService->getOrderContactForm(),
                            $case,
                            $flightRequest->fr_project_id
                        );

                        $originProductQuote = $reProtectionCreateService->createOriginProductQuoteInfrastructure(
                            $boRequestReProtectionService->getOrderCreateFromSaleForm(),
                            $saleData,
                            $order,
                            $case,
                            $this->flight_request_is_automate
                        );
                        $productQuoteChange = ProductQuoteChange::createReProtection(
                            $originProductQuote->pq_id,
                            $case->cs_id,
                            $this->flight_request_is_automate,
                            $flightRequestService->getIsRefundAllowed()
                        );
                        $productQuoteChangeRepository->save($productQuoteChange);
                        $caseReProtectionService->setCaseDeadline($originProductQuote->flightQuote);
                    } catch (Throwable $throwable) {
                        $case->addEventLog(CaseEventLog::RE_PROTECTION_CREATE, 'Order not created');
                        $caseReProtectionService->caseToManual('Order not created');

                        if (isset($case, $flightRequest) && $client === null) {
                            $client = $reProtectionCreateService->createSimpleClient($flightRequest->fr_project_id);
                            $caseReProtectionService->additionalFillingCase($client->id, $flightRequest->fr_project_id);
                        }
                        $flightRequestService->error(VarDumper::dumpAsString($throwable->getMessage()));
                        $reProtectionCreateService::writeLog($throwable);
                        return;
                    }
                }
            }

            if (!isset($order) && !$order = $originProductQuote->pqOrder) {
                throw new DomainException('Order not found');
            }

            if (!CaseOrder::find()->where(['co_order_id' => $order->or_id, 'co_case_id' => $case->cs_id])->exists()) {
                $caseOrder = CaseOrder::create($case->cs_id, $order->or_id);
                $caseOrder->detachBehavior('user');
                if (!$caseOrder->save()) {
                    throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($caseOrder));
                }
            }

            try {
                $reProtectionCreateService::casesRefundProcessing($flightRequest->fr_booking_id, $case);
            } catch (\Throwable $throwable) {
                Yii::warning(AppHelper::throwableLog($throwable), 'ReprotectionCreateJob:CasesRefundProcessing');
            }

            try {
                $reProtectionCreateService::casesVoluntaryChangesProcessing($flightRequest->fr_booking_id, $case);
            } catch (\Throwable $throwable) {
                Yii::warning(AppHelper::throwableLog($throwable), 'ReprotectionCreateJob:CasesVoluntaryChangesProcessing');
            }

            try {
                $reProtectionCreateService->originProductQuoteDecline($originProductQuote, $case);
            } catch (Throwable $throwable) {
                $case->addEventLog(CaseEventLog::RE_PROTECTION_CREATE, 'Flight quote not updated');
                $caseReProtectionService->caseToManual('Flight quote not updated');
                throw new DomainException('OriginProductQuote not declined');
            }

            $reProtectionCreateService->declineReProtectionQuotes($originProductQuote->pq_id, $originProductQuote->pq_gid, $case);

            if (empty($flightRequest->getFlightQuoteData())) {
                $case->addEventLog(CaseEventLog::RE_PROTECTION_CREATE, 'New schedule change happened, no quote provided');
                $caseReProtectionService->caseToManual('New schedule change happened, no quote provided');
                throw new DomainException('New schedule change happened, no quote provided');
            }

            try {
                $flight = $reProtectionCreateService->getFlightByOriginQuote($originProductQuote);
                $flightQuote = $flightQuoteManageService->createReProtection(
                    $flight,
                    $flightRequest->getFlightQuoteData(),
                    null,
                    null,
                    $case,
                    null,
                    null,
                    $originProductQuote ?? null
                );
                $caseReProtectionService->setCaseDeadline($flightQuote);
                $reProtectionQuote = $flightQuote->fqProductQuote;
                $reProtectionCreateService->recommendedReProtection($originProductQuote->pq_id, $reProtectionQuote->pq_id);
            } catch (\Throwable $throwable) {
                $case->addEventLog(CaseEventLog::RE_PROTECTION_CREATE, 'Could not create new reProtection quote');
                $caseReProtectionService->caseToManual('Could not create new reProtection quote');
                $flightRequestService->error(VarDumper::dumpAsString($throwable->getMessage()));
                $reProtectionCreateService::writeLog($throwable);
                return;
            }

            if (!isset($productQuoteChange)) {
                $productQuoteChange = ProductQuoteChange::createReProtection(
                    $originProductQuote->pq_id,
                    $case->cs_id,
                    $this->flight_request_is_automate,
                    $flightRequestService->getIsRefundAllowed()
                );
                $productQuoteChangeRepository->save($productQuoteChange);
            }

            try {
                ProductQuoteChangeRelationService::getOrCreate($productQuoteChange->pqc_id, $reProtectionQuote->pq_id);
            } catch (\Throwable $throwable) {
                Yii::error(AppHelper::throwableLog($throwable), 'ReprotectionCreateJob:ProductQuoteChangeRelation');
            }

            $reProtectionCreateService->setProductQuoteChangeIsAutomate($productQuoteChange, (bool) $this->flight_request_is_automate);

            if ($productQuoteChange->isStatusNew() || $productQuoteChange->isPending()) {
                if ($case->isError() || $case->isTrash() || $case->isAwaiting() || $case->isSolved()) {
                    if (!$case->cs_user_id) {
                        $case->addEventLog(CaseEventLog::RE_PROTECTION_CREATE, 'New reprotection request');
                        $caseReProtectionService->caseToManual('New reprotection request');
                    } else {
                        $caseReProtectionService->caseNeedAction();
                        $linkToCase = Purifier::createCaseShortLink($case);
                        Notifications::createAndPublish(
                            $case->cs_user_id,
                            'New reProtection quote has been added',
                            'New reProtection quote has been added for Case: (' . $linkToCase . '). Manual action required',
                            Notifications::TYPE_INFO,
                            true
                        );
                    }
                    return;
                }

                if ($this->flight_request_is_automate) {
                    if ($case->isPending() || $case->isStatusAutoProcessing() || $case->isFollowUp()) {
                        $caseReProtectionService->caseToAutoProcessing('Automatic processing requested');
                    }

                    if ($case->cs_user_id && $case->isProcessing()) {
                        $caseReProtectionService->caseNeedAction();
                        $linkToCase = Purifier::createCaseShortLink($case);
                        Notifications::createAndPublish(
                            $case->cs_user_id,
                            'New reProtection quote has been added',
                            'New reProtection quote has been added for Case: (' . $linkToCase . ') ',
                            Notifications::TYPE_INFO,
                            true
                        );
                    }

                    try {
                        (new OtaRequestReProtectionService($flightRequest, $reProtectionQuote, $originProductQuote, $case))->send();
                    } catch (\Throwable $throwable) {
                        $case->addEventLog(CaseEventLog::RE_PROTECTION_CREATE, 'Request HybridService is failed');
                        $caseReProtectionService->caseToManual('OTA site is not informed');
                        $flightRequestService->pending('OTA site is not informed');
                        return;
                    }

                    try {
                        $sendEmailReProtectionService->processing(
                            $case,
                            $order ?? null,
                            $reProtectionQuote,
                            $originProductQuote,
                            $productQuoteChange
                        );

                        $productQuoteChange->statusToPending();
                        $productQuoteChangeRepository->save($productQuoteChange);
                        $eventDispatcher->dispatch(new ProductQuoteChangeAutoDecisionPendingEvent($productQuoteChange->pqc_id));
                        $flightRequestService->done('Client Email send');
                    } catch (\Throwable $throwable) {
                        $case->addEventLog(CaseEventLog::RE_PROTECTION_CREATE, 'Auto SCHD Email not sent');
                        $caseReProtectionService->caseToManual('Auto SCHD Email not sent');
                        $flightRequestService->pending(VarDumper::dumpAsString($throwable->getMessage()));
                    }

                    try {
                        $productQuoteDataManageService->updateRecommendedChangeQuote($originProductQuote->pq_id, $reProtectionQuote->pq_id);
                        $case->addEventLog(CaseEventLog::RE_PROTECTION_CREATE, 'Set recommended quote(' . $reProtectionQuote->pq_gid . ')');
                    } catch (\Throwable $throwable) {
                        $case->addEventLog(CaseEventLog::RE_PROTECTION_CREATE, 'Quote(' . $reProtectionQuote->pq_gid . ') not set recommended');
                    }
                    return;
                }

                if (!$this->flight_request_is_automate) {
                    if ($case->cs_user_id && $case->isProcessing()) {
                        $caseReProtectionService->caseNeedAction();
                        $linkToCase = Purifier::createCaseShortLink($case);
                        Notifications::createAndPublish(
                            $case->cs_user_id,
                            'New reProtection quote has been added',
                            'New reProtection quote has been added for Case: (' . $linkToCase . '). Manual action required',
                            Notifications::TYPE_INFO,
                            true
                        );
                        return;
                    }

                    if ($case->isPending() || $case->isStatusAutoProcessing() || $case->isFollowUp() || $case->isStatusNew()) {
                        $case->addEventLog(CaseEventLog::RE_PROTECTION_CREATE, 'Manual processing requested');
                        $caseReProtectionService->caseToManual('Manual processing requested');
                        return;
                    }
                }
            }
            \Yii::info(
                [
                    'case' => ArrayHelper::merge($case->toArray(), ['status' => CasesStatus::getName($case->cs_status)]),
                    'productQuoteChange' => ArrayHelper::merge($productQuoteChange->toArray(), ['status' => ProductQuoteChangeStatus::getName($productQuoteChange->pqc_status_id)]),
                ],
                'info\ReprotectionCreateJob:UnknownProcessException'
            );

            throw new DomainException('Unknown process exception');
        } catch (Throwable $throwable) {
            if (isset($flightRequest)) {
                $data['flightRequest'] = $flightRequest->toArray();
            }
            $reProtectionCreateService::writeLog($throwable, $data ?? []);
            if ($flightRequestService->getFlightRequest()) {
                $flightRequestService->error(VarDumper::dumpAsString($throwable->getMessage()));
            }

            if (isset($case, $flightRequest) && $client === null) {
                $client = $reProtectionCreateService->createSimpleClient($flightRequest->fr_project_id);
                $caseReProtectionService->additionalFillingCase($client->id, $flightRequest->fr_project_id);
            }
        }
    }
}
