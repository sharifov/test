<?php

namespace common\components\jobs;

use common\components\HybridService;
use common\components\purifier\Purifier;
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
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use modules\product\src\repositories\ProductQuoteRelationRepository;
use sales\dispatchers\EventDispatcher;
use sales\entities\cases\CaseCategory;
use sales\entities\cases\CaseEventLog;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesStatus;
use sales\exception\BoResponseException;
use sales\exception\CheckRestrictionException;
use sales\exception\ValidationException;
use sales\helpers\app\AppHelper;
use sales\helpers\ErrorsToStringHelper;
use sales\helpers\setting\SettingHelper;
use sales\services\cases\CasesCommunicationService;
use sales\services\cases\CasesSaleService;
use sales\services\email\SendEmailByCase;
use Throwable;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;
use yii\queue\Queue;
use sales\repositories\product\ProductQuoteRepository;

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
        $this->executionTimeRegister();

        $reProtectionCreateService = Yii::createObject(ReprotectionCreateService::class);
        $flightQuoteManageService = Yii::createObject(FlightQuoteManageService::class);
        $caseReProtectionService = Yii::createObject(CaseReProtectionService::class);
        $boRequestReProtectionService = Yii::createObject(BoRequestReProtectionService::class);
        $sendEmailReProtectionService = Yii::createObject(SendEmailReProtectionService::class);
        $productQuoteChangeRepository = Yii::createObject(ProductQuoteChangeRepository::class);
        $flightRequestService = Yii::createObject(FlightRequestService::class);
        $eventDispatcher = Yii::createObject(EventDispatcher::class);

        $client = null;

        try {
            if (!$flightRequest = FlightRequest::findOne($this->flight_request_id)) {
                throw new DomainException('FlightRequest not found, ID (' . $this->flight_request_id . ')');
            }
            $flightRequestService->setFlightRequest($flightRequest);

            $originProductQuote = ProductQuoteQuery::getProductQuoteByBookingId($flightRequest->fr_booking_id);
            if ($originProductQuote && ProductQuoteChangeQuery::existsByQuoteIdAndStatuses($originProductQuote->pq_id, ProductQuoteChangeStatus::PROCESSING_LIST)) {
                $statusNames = implode(', ', ProductQuoteChangeStatus::getNames(ProductQuoteChangeStatus::PROCESSING_LIST));
                $flightRequestService->error('Reason: Product Quote Change exist in status (' . $statusNames . ')');
                return;
            }

            if (!$originProductQuote) {
                try {
                    if (!$case = $caseReProtectionService::getLastActiveCaseByBookingId($flightRequest->fr_booking_id)) {
                        $case = $caseReProtectionService->createCase($flightRequest);
                    }
                    $caseReProtectionService->setCase($case);

                    $saleData = $boRequestReProtectionService->getSaleData($flightRequest->fr_booking_id, $case);

                    $client = $reProtectionCreateService->getOrCreateClient(
                        $flightRequest->fr_project_id,
                        $boRequestReProtectionService->getOrderContactForm()
                    );

                    $caseReProtectionService->additionalFillingCase($client->id, $flightRequest->fr_project_id);
                    $reProtectionCreateService->createCaseSale($saleData, $case);

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
                    $caseReProtectionService->setCaseDeadline($originProductQuote->flightQuote);
                } catch (Throwable $throwable) {
                    if (isset($case)) {
                        $caseReProtectionService->caseToManual('Order not created');
                    }
                    if (isset($case, $flightRequest) && $client === null) {
                        $client = $reProtectionCreateService->createSimpleClient($flightRequest->fr_project_id);
                        $caseReProtectionService->additionalFillingCase($client->id, $flightRequest->fr_project_id);
                    }
                    $flightRequestService->error(VarDumper::dumpAsString($throwable->getMessage()));
                    $reProtectionCreateService::writeLog($throwable);
                    return;
                }
            }

            if (!isset($case) && !$case = $caseReProtectionService::findCase($flightRequest->fr_booking_id, $originProductQuote)) {
                throw new DomainException('Case not found');
            }

            $caseReProtectionService->setCase($case);

            try {
                $reProtectionCreateService->originProductQuoteDecline($originProductQuote, $case);
            } catch (Throwable $throwable) {
                $caseReProtectionService->caseToManual('Flight quote not updated');
                throw new DomainException('OriginProductQuote not declined');
            }

            $reProtectionCreateService->declineReProtectionQuotes($originProductQuote->pq_id, $originProductQuote->pq_gid, $case);

            if (empty($flightRequest->getFlightQuoteData())) {
                $caseReProtectionService->caseToManual('New schedule change happened, no quote provided');
                throw new DomainException('New schedule change happened, no quote provided');
            }

            try {
                $flight = $reProtectionCreateService->getFlightByOriginQuote($originProductQuote);
                $flightQuote = $flightQuoteManageService->createReProtection(
                    $flight,
                    $flightRequest->getFlightQuoteData(),
                    $originProductQuote->pq_order_id,
                    null,
                    $case,
                    null,
                    null,
                    $originProductQuote ?? null
                );
                $caseReProtectionService->setCaseDeadline($flightQuote);
                $reProtectionQuote = $flightQuote->fqProductQuote;
            } catch (\Throwable $throwable) {
                $caseReProtectionService->caseToManual('Could not create new reProtection quote');
                $flightRequestService->error(VarDumper::dumpAsString($throwable->getMessage()));
                $reProtectionCreateService::writeLog($throwable);
                return;
            }

            if (!SettingHelper::isEnableSendHookToOtaReProtectionCreate()) {
                \Yii::warning('Setting "enable_send_hook_to_ota_re_protection_create" is disabled', 'ReprotectionCreateJob:warning');
            }

            if ($lastProductQuoteChange = $originProductQuote->productQuoteLastChange) {
                /** @var ProductQuoteChange $lastProductQuoteChange */
                if ($lastProductQuoteChange->isStatusNew() || $lastProductQuoteChange->isDecisionPending()) {
                    if ($caseReProtectionService->isAutomateProcessing($flightRequest)) {
                        $caseReProtectionService->caseToAutoProcessing('Automatic processing requested');

                        if (SettingHelper::isEnableSendHookToOtaReProtectionCreate()) {
                            try {
                                (new OtaRequestReProtectionService($flightRequest, $reProtectionQuote, $originProductQuote, $case))->send();
                            } catch (\Throwable $throwable) {
                                $case->addEventLog(CaseEventLog::RE_PROTECTION_CREATE, 'Request HybridService is failed');
                                $caseReProtectionService->caseToManual('OTA site is not informed');
                                $flightRequestService->pending('OTA site is not informed');
                                return;
                            }
                        }

                        try {
                            $sendEmailReProtectionService->processing(
                                $case,
                                $order ?? null,
                                $reProtectionQuote,
                                $originProductQuote,
                                $lastProductQuoteChange
                            );

                            $lastProductQuoteChange->decisionPending();
                            $productQuoteChangeRepository->save($lastProductQuoteChange);
                            $eventDispatcher->dispatch(new ProductQuoteChangeAutoDecisionPendingEvent($lastProductQuoteChange->pqc_id));
                            $flightRequestService->done('Client Email send');
                        } catch (\Throwable $throwable) {
                            $caseReProtectionService->caseToManual('Auto SCHD Email not sent');
                            $flightRequestService->pending(VarDumper::dumpAsString($throwable->getMessage()));
                        }
                        return;
                    }

                    if ($case->isProcessing()) {
                        $linkToCase = Purifier::createCaseShortLink($case);
                        Notifications::createAndPublish(
                            $case->cs_user_id,
                            'New reProtection quote has been added',
                            'New reProtection quote has been added for Case: id (' . $case->cs_id . ') ' . $linkToCase,
                            Notifications::TYPE_INFO,
                            true
                        );

                        if (SettingHelper::isEnableSendHookToOtaReProtectionCreate()) {
                            try {
                                (new OtaRequestReProtectionService($flightRequest, $reProtectionQuote, $originProductQuote, $case))->send();
                            } catch (\Throwable $throwable) {
                                $case->addEventLog(CaseEventLog::RE_PROTECTION_CREATE, 'Request HybridService is failed');
                                $caseReProtectionService->caseToManual('OTA site is not informed');
                                $flightRequestService->pending('OTA site is not informed');
                                return;
                            }
                        }

                        try {
                            $sendEmailReProtectionService->processing(
                                $case,
                                $order ?? null,
                                $reProtectionQuote,
                                $originProductQuote,
                                $lastProductQuoteChange
                            );
                            $flightRequestService->done('Client Email send');
                        } catch (\Throwable $throwable) {
                            $case->addEventLog(CaseEventLog::RE_PROTECTION_CREATE, 'Auto SCHD Email not sent');
                            $flightRequestService->pending(VarDumper::dumpAsString($throwable->getMessage()));
                        }
                        return;
                    }
                }
            }

            if (!$originProductQuote->productQuoteLastChange || !$originProductQuote->productQuoteLastChange->isStatusNew()) {
                $lastProductQuoteChange = ProductQuoteChange::createNew($originProductQuote->pq_id, $case->cs_id, $this->flight_request_is_automate);
                $productQuoteChangeRepository->save($lastProductQuoteChange);
            }

            if ($this->flight_request_is_automate === false) {
                $caseReProtectionService->caseToManual('Manual processing requested');
                $flightRequestService->pending('Manual processing requested');
                return;
            }

            if (SettingHelper::isEnableSendHookToOtaReProtectionCreate()) {
                try {
                    (new OtaRequestReProtectionService($flightRequest, $reProtectionQuote, $originProductQuote, $case))->send();
                } catch (\Throwable $throwable) {
                    $case->addEventLog(CaseEventLog::RE_PROTECTION_CREATE, 'Request HybridService is failed');
                    $caseReProtectionService->caseToManual('OTA site is not informed');
                    $flightRequestService->pending('OTA site is not informed');
                    return;
                }
            }

            try {
                $sendEmailReProtectionService->processing(
                    $case,
                    $order ?? null,
                    $reProtectionQuote,
                    $originProductQuote,
                    $lastProductQuoteChange
                );

                $lastProductQuoteChange->decisionPending();
                $productQuoteChangeRepository->save($lastProductQuoteChange);
                $eventDispatcher->dispatch(new ProductQuoteChangeAutoDecisionPendingEvent($lastProductQuoteChange->pqc_id));

                $caseReProtectionService->caseToAutoProcessing('Automatic processing requested');
                $flightRequestService->done('Client Email send');
            } catch (\Throwable $throwable) {
                $caseReProtectionService->caseToManual('Auto SCHD Email not sent');
                $flightRequestService->pending(VarDumper::dumpAsString($throwable->getMessage()));
            }
        } catch (Throwable $throwable) {
            $reProtectionCreateService::writeLog($throwable);
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
