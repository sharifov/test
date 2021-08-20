<?php

namespace common\components\jobs;

use common\components\HybridService;
use common\models\ClientEmail;
use DomainException;
use modules\flight\models\FlightRequest;
use modules\flight\src\useCases\flightQuote\FlightQuoteManageService;
use modules\flight\src\useCases\reprotectionCreate\service\ReprotectionCreateService;
use modules\flight\src\useCases\sale\form\OrderContactForm;
use modules\order\src\services\createFromSale\OrderCreateFromSaleForm;
use modules\order\src\services\createFromSale\OrderCreateFromSaleService;
use modules\product\src\entities\productQuote\ProductQuoteQuery;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeQuery;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeRepository;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use modules\product\src\repositories\ProductQuoteRelationRepository;
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
use Yii;
use yii\queue\JobInterface;
use yii\queue\Queue;
use sales\repositories\product\ProductQuoteRepository;

/**
 * @property int $flight_request_id
 */
class ReprotectionCreateJob extends BaseJob implements JobInterface
{
    public $flight_request_id;

    /**
     * @param Queue $queue
     * @return bool
     */
    public function execute($queue): void
    {
        $this->executionTimeRegister();

        $reProtectionCreateService = Yii::createObject(ReprotectionCreateService::class);
        $casesSaleService = Yii::createObject(CasesSaleService::class);
        $flightQuoteManageService = Yii::createObject(FlightQuoteManageService::class);
        $productQuoteRelationRepository = Yii::createObject(ProductQuoteRelationRepository::class);
        $productQuoteRepository = Yii::createObject(ProductQuoteRepository::class);
        $orderCreateFromSaleService = Yii::createObject(OrderCreateFromSaleService::class);
        $casesCommunicationService = Yii::createObject(CasesCommunicationService::class);

        $client = null;

        try {
            if (!$flightRequest = FlightRequest::findOne($this->flight_request_id)) {
                throw new DomainException('FlightRequest not found, ID (' . $this->flight_request_id . ')');
            }

            $caseExist = Cases::find()->where(['cs_order_uid' => $flightRequest->fr_booking_id])
                ->andWhere(['NOT IN', 'cs_status', [CasesStatus::STATUS_SOLVED, CasesStatus::STATUS_TRASH]])
                ->innerJoin(CaseCategory::tableName(), 'cs_category_id = cc_id and cc_key = :categoryKey', [
                    'categoryKey' => SettingHelper::getReProtectionCaseCategory()
                ])->exists();

            $oldProductQuote = ProductQuoteQuery::getProductQuoteByBookingId($flightRequest->fr_booking_id);
            if ($caseExist || ($oldProductQuote && ProductQuoteChangeQuery::existsByQuoteIdAndStatuses($oldProductQuote->pq_id, ProductQuoteChangeStatus::PROCESSING_LIST))) {
                $flightRequest->statusToError();
                $reProtectionCreateService->flightRequestChangeStatus(
                    $flightRequest,
                    FlightRequest::STATUS_ERROR,
                    'Reason: Product Quote Change exist in status id: ' . implode(', ', ProductQuoteChangeStatus::PROCESSING_LIST)
                );
                return;
            }

            $case = $reProtectionCreateService->createCase($flightRequest);
            $case->addEventLog(
                CaseEventLog::CASE_CREATED,
                'Created Schedule Change case, BookingID: ' . $flightRequest->fr_booking_id,
                ['case_gid' => $case->cs_gid, 'fr_booking_id' => $flightRequest->fr_booking_id]
            );

            if ($order = $reProtectionCreateService->getOrderByBookingId($flightRequest->fr_booking_id)) {
                $case->addEventLog(
                    CaseEventLog::RE_PROTECTION_CREATE,
                    'Found Order GID: (' . $order->or_gid . ')',
                    ['order_gid' => $order->or_gid, 'order_id' => $order->or_id]
                );
                if (!$oldProductQuote) {
                    $case->addEventLog(
                        CaseEventLog::RE_PROTECTION_CREATE,
                        'Origin ProductQuote not found',
                        ['fr_booking_id' => $flightRequest->fr_booking_id]
                    );
                    $reProtectionCreateService->caseToManual($case, 'Flight quote not updated');
                    $reProtectionCreateService->flightRequestChangeStatus(
                        $flightRequest,
                        FlightRequest::STATUS_PENDING,
                        'Original quote not declined'
                    );
                } else {
                    $case->addEventLog(
                        CaseEventLog::RE_PROTECTION_CREATE,
                        'Origin ProductQuote found (' . $oldProductQuote->pq_gid . ')',
                        ['pq_gid' => $oldProductQuote->pq_gid]
                    );
                    $reProtectionCreateService->declineOldProductQuote($order);
                    $case->addEventLog(
                        CaseEventLog::RE_PROTECTION_CREATE,
                        'Old ProductQuotes declined',
                        ['order_id' => $order->or_id, 'order_gid' => $order->or_gid]
                    );
                }

                if (
                    !$client = $reProtectionCreateService->getClientByOrderProject(
                        $order->getId(),
                        $flightRequest->fr_project_id
                    )
                ) {
                    $case->addEventLog(
                        CaseEventLog::RE_PROTECTION_CREATE,
                        'Client not found',
                        ['order_gid' => $order->or_gid, 'order_id' => $order->or_id]
                    );
                    $reProtectionCreateService->caseToManual(
                        $case,
                        'Client not found in OrderContact. Created default client.'
                    );
                    $client = $reProtectionCreateService->createSimpleClient($flightRequest->fr_project_id);
                    $case->addEventLog(CaseEventLog::RE_PROTECTION_CREATE, 'Created default client');
                }
                $reProtectionCreateService->additionalFillingCase($case, $client->id, $flightRequest->fr_project_id);
                $orderCreateFromSaleService->caseOrderRelation($order->getId(), $case->cs_id);
                $case->addEventLog(
                    CaseEventLog::RE_PROTECTION_CREATE,
                    'Case related to order',
                    ['order_gid' => $order->or_gid, 'case_gid' => $case->cs_gid]
                );

                try {
                    if (
                        empty($flightRequest->getFlightQuoteData()) ||
                        !is_array($flightRequest->getFlightQuoteData())
                    ) {
                        throw new CheckRestrictionException('FlightQuote is empty/wrong data in FlightRequest/data_json');
                    }
                    if (!$flight = $reProtectionCreateService->getFlight($order)) {
                        throw new CheckRestrictionException('Flight not found');
                    }

                    $flightQuote = $flightQuoteManageService->createReProtection(
                        $flight,
                        $flightRequest->getFlightQuoteData(),
                        $order->getId(),
                        null,
                        $case->cs_id
                    );
                    $case->addEventLog(
                        CaseEventLog::RE_PROTECTION_CREATE,
                        'FlightQuote created GID: ' . ($flightQuote->fqProductQuote->pq_gid ?? '-'),
                        ['pq_gid' => $flightQuote->fqProductQuote->pq_gid ?? null]
                    );
                    if ($oldProductQuote) {
                        $relation = ProductQuoteRelation::createReProtection(
                            $oldProductQuote->pq_id,
                            $flightQuote->fq_product_quote_id
                        );
                        $productQuoteRelationRepository->save($relation);
                        $productQuoteChange = ProductQuoteChange::createNew($oldProductQuote->pq_id, $case->cs_id);
                        (new ProductQuoteChangeRepository())->save($productQuoteChange);
                        $case->addEventLog(
                            CaseEventLog::RE_PROTECTION_CREATE,
                            'ProductQuote related to origin Product Quote (ReProtection Type)',
                            ['pq_id' => $oldProductQuote->pq_id, 'case_id' => $case->cs_id]
                        );
                    }
                } catch (\Throwable $throwable) {
                    $case->addEventLog(
                        CaseEventLog::RE_PROTECTION_CREATE,
                        'FlightQuote not created',
                        ['order_id' => $order->getId(), 'case_id' => $case->cs_id]
                    );
                    $reProtectionCreateService->caseToManual($case, 'New quote not created');
                    throw new CheckRestrictionException($throwable->getMessage());
                }
            } else {
                try {
                    $case->addEventLog(CaseEventLog::RE_PROTECTION_CREATE, 'Order not found');
                    $case->addEventLog(
                        CaseEventLog::RE_PROTECTION_CREATE,
                        'START: Request getSaleFrom BackOffice, BookingID: ' . $flightRequest->fr_booking_id,
                        ['fr_booking_id' => $flightRequest->fr_booking_id]
                    );
                    $saleSearch = $casesSaleService->getSaleFromBo($flightRequest->fr_booking_id);

                    if (empty($saleSearch['saleId'])) {
                        throw new BoResponseException('Sale not found by Booking ID(' . $flightRequest->fr_booking_id . ') from "cs/search"');
                    }
                    $case->addEventLog(
                        CaseEventLog::RE_PROTECTION_CREATE,
                        'START: Request DetailRequestToBackOffice SaleID: ' . $saleSearch['saleId'],
                        ['sale_id' => $saleSearch['saleId']]
                    );
                    $saleData = $casesSaleService->detailRequestToBackOffice($saleSearch['saleId'], 0, 120, 1);

                    $orderCreateFromSaleForm = new OrderCreateFromSaleForm();
                    if (!$orderCreateFromSaleForm->load($saleData)) {
                        throw new \DomainException('OrderCreateFromSaleForm not loaded');
                    }
                    if (!$orderCreateFromSaleForm->validate()) {
                        throw new ValidationException(ErrorsToStringHelper::extractFromModel($orderCreateFromSaleForm));
                    }
                    $orderContactForm = OrderContactForm::fillForm($saleData);
                    if (!$orderContactForm->validate()) {
                        throw new ValidationException(ErrorsToStringHelper::extractFromModel($orderContactForm));
                    }
                    $case->addEventLog(CaseEventLog::RE_PROTECTION_CREATE, 'Responses accepted successfully');

                    $client = $reProtectionCreateService->getOrCreateClient(
                        $flightRequest->fr_project_id,
                        $orderContactForm
                    );
                    $reProtectionCreateService->additionalFillingCase(
                        $case,
                        $client->id,
                        $flightRequest->fr_project_id
                    );

                    $casesSaleService->createSaleByData($case->cs_id, $saleData);
                    $case->addEventLog(
                        CaseEventLog::RE_PROTECTION_CREATE,
                        'Case Sale created by Data',
                        ['case_id' => $case->cs_id]
                    );
                    $order = $reProtectionCreateService->createOrder(
                        $orderCreateFromSaleForm,
                        $orderContactForm,
                        $case,
                        $flightRequest->fr_project_id
                    );
                    $case->addEventLog(
                        CaseEventLog::RE_PROTECTION_CREATE,
                        'Order created GID: ' . $order->or_gid,
                        ['order_gid' => $order->or_gid]
                    );

                    $oldProductQuote = $reProtectionCreateService->createFlightInfrastructure(
                        $orderCreateFromSaleForm,
                        $saleData,
                        $order
                    );
                    $case->addEventLog(
                        CaseEventLog::RE_PROTECTION_CREATE,
                        'Origin ProductQuote created GID: ' . $oldProductQuote->pq_gid,
                        ['pq_gid' => $oldProductQuote->pq_gid]
                    );
                    $oldProductQuote->declined();
                    $productQuoteRepository->save($oldProductQuote);
                    $case->addEventLog(
                        CaseEventLog::RE_PROTECTION_CREATE,
                        'Origin ProductQuote declined',
                        ['pq_gid' => $oldProductQuote->pq_gid]
                    );
                    $productQuoteChange = ProductQuoteChange::createNew($oldProductQuote->pq_id, $case->cs_id);
                    (new ProductQuoteChangeRepository())->save($productQuoteChange);

                    $reProtectionCreateService->createPayment($orderCreateFromSaleForm, $saleData, $order);
                } catch (\Throwable $throwable) {
                    $reProtectionCreateService->caseToManual($case, 'Order not created');
                    throw new CheckRestrictionException($throwable->getMessage());
                }

                try {
                    if (empty($flightRequest->getFlightQuoteData()) || !is_array($flightRequest->getFlightQuoteData())) {
                        throw new CheckRestrictionException('FlightQuote empty/wrong data in FlightRequest/data_json');
                    }
                    if (!$flight = $reProtectionCreateService->getFlightByBookingId($flightRequest->fr_booking_id)) {
                        throw new CheckRestrictionException('Flight by BookingId not found');
                    }

                    $flightQuote = $flightQuoteManageService->createReProtection(
                        $flight,
                        $flightRequest->getFlightQuoteData(),
                        $order->getId(),
                        null,
                        $case->cs_id
                    );


                    $case->addEventLog(
                        CaseEventLog::RE_PROTECTION_CREATE,
                        'FlightQuote created GID: ' . ($flightQuote->fqProductQuote->pq_gid ?? '-'),
                        ['pq_gid' => $flightQuote->fqProductQuote->pq_gid ?? null]
                    );

                    if ($oldProductQuote) {
                        $relation = ProductQuoteRelation::createReProtection(
                            $oldProductQuote->pq_id,
                            $flightQuote->fq_product_quote_id
                        );
                        $productQuoteRelationRepository->save($relation);
                        $case->addEventLog(
                            CaseEventLog::RE_PROTECTION_CREATE,
                            'ProductQuote related to origin Product Quote (ReProtection Type)',
                            ['old_pq_id' => $oldProductQuote->pq_id, 'new_pq_id' => $flightQuote->fq_product_quote_id]
                        );
                    }
                } catch (\Throwable $throwable) {
                    $reProtectionCreateService->caseToManual($case, 'New quote not created');
                    throw new CheckRestrictionException($throwable->getMessage());
                }
            }

            if ($flightRequest->getIsAutomateDataJson() === false && $case->isAutomate()) {
                $reProtectionCreateService->caseToManual($case, 'Manual processing requested');
                $reProtectionCreateService->flightRequestChangeStatus(
                    $flightRequest,
                    FlightRequest::STATUS_PENDING,
                    'Manual processing requested'
                );
            }

            if (SettingHelper::isEnableSendHookToOtaReProtectionCreate()) {
                try {
                    $hybridService = Yii::createObject(HybridService::class);
                    $data = [
                        'data' => [
                            'booking_id' => $flightRequest->fr_booking_id,
                            'reprotection_quote_gid' => $flightQuote->fqProductQuote->pq_gid,
                            'case_gid' => $case->cs_gid,
                        ]
                    ];
                    if (!$hybridService->whReprotection($flightRequest->fr_project_id, $data)) {
                        throw new CheckRestrictionException(
                            'Not found webHookEndpoint in project (' . $flightRequest->fr_project_id . ')'
                        );
                    }
                    $case->addEventLog(CaseEventLog::RE_PROTECTION_CREATE, 'Request HybridService sent successfully');
                } catch (\Throwable $throwable) {
                    $case->addEventLog(CaseEventLog::RE_PROTECTION_CREATE, 'Request HybridService is failed');
                    $reProtectionCreateService->caseToManual($case, 'OTA site is not informed');
                    throw new CheckRestrictionException($throwable->getMessage());
                }
            }

            $reProtectionCreateService->setCaseDeadline($case, $flightQuote);

            if ($case->isAutomate()) {
                try {
                    if (!$clientId = $case->cs_client_id) {
                        throw new CheckRestrictionException('Client not found in Case');
                    }
                    $clientEmail = '';
                    if ($order && $order->orderContacts) {
                        $clientEmail = $order->orderContacts[0]->oc_email;
                    }
                    if (!$clientEmail && !$clientEmail = ClientEmail::getGeneralEmail($clientId)) {
                        throw new CheckRestrictionException('ClientEmail not found');
                    }

                    $emailData = $casesCommunicationService->getEmailDataWithoutAgentData($case);
                    $emailData['reprotection_quote'] = $flightQuote->fqProductQuote->serialize();
                    if ($oldProductQuote) {
                        $emailData['original_quote'] = $oldProductQuote->serialize();
                    }

                    $resultStatus = (new SendEmailByCase($case->cs_id, $clientEmail, $emailData))->getResultStatus();
                    if ($resultStatus === SendEmailByCase::RESULT_NOT_ENABLE) {
                        throw new CheckRestrictionException('ClientEmail not send. EmailConfigs not enabled.');
                    }
                    if ($resultStatus !== SendEmailByCase::RESULT_SEND) {
                        throw new CheckRestrictionException('ClientEmail not send');
                    }

                    if ($oldProductQuote && isset($productQuoteChange)) {
                        $productQuoteChange->decisionPending();
                        (new ProductQuoteChangeRepository())->save($productQuoteChange);
                    }
                    $case->addEventLog(CaseEventLog::RE_PROTECTION_CREATE, 'Email sent successfully');
                    $reProtectionCreateService->caseToAutoProcessing($case);
                    $reProtectionCreateService->flightRequestChangeStatus(
                        $flightRequest,
                        FlightRequest::STATUS_DONE,
                        'Client Email send'
                    );
                } catch (\Throwable $throwable) {
                    $reProtectionCreateService->caseToManual($case, 'Auto SCHD Email not sent');
                    $reProtectionCreateService->flightRequestChangeStatus(
                        $flightRequest,
                        FlightRequest::STATUS_PENDING,
                        $throwable->getMessage()
                    );
                }
            }
        } catch (\Throwable $throwable) {
            $message['throwable'] = AppHelper::throwableLog($throwable);
            $message['flightRequestID'] = $this->flight_request_id;
            if ($throwable instanceof DomainException) {
                \Yii::warning($message, 'ReprotectionCreateJob:throwable');
            } else {
                \Yii::error($message, 'ReprotectionCreateJob:throwable');
            }

            if (isset($flightRequest)) {
                $reProtectionCreateService->flightRequestChangeStatus(
                    $flightRequest,
                    FlightRequest::STATUS_ERROR,
                    $throwable->getMessage()
                );
            }
            if (isset($case, $flightRequest) && $client === null) {
                $client = $reProtectionCreateService->createSimpleClient($flightRequest->fr_project_id);
                $reProtectionCreateService->additionalFillingCase($case, $client->id, $flightRequest->fr_project_id);
            }
        }
    }
}
