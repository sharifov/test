<?php

namespace modules\product\controllers;

use common\components\hybrid\HybridWhData;
use common\components\HybridService;
use common\models\Email;
use common\models\EmailTemplateType;
use common\models\Notifications;
use common\models\UserProjectParams;
use modules\flight\models\FlightQuoteFlight;
use modules\flight\src\useCases\reprotectionDecision;
use modules\order\src\entities\order\Order;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteQuery;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeRepository;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use modules\product\src\entities\productQuoteChange\service\ProductQuoteChangeService;
use modules\product\src\entities\productQuoteData\service\ProductQuoteDataManageService;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefund;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundStatus;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use modules\product\src\forms\ChangeQuoteSendEmailForm;
use modules\product\src\forms\ReprotectionQuotePreviewEmailForm;
use modules\product\src\forms\ReprotectionQuoteSendEmailForm;
use modules\product\src\forms\VoluntaryChangeQuotePreviewEmailForm;
use modules\product\src\services\productQuote\ProductQuoteCloneService;
use src\auth\Auth;
use src\dispatchers\EventDispatcher;
use src\entities\cases\CaseEventLog;
use src\entities\cases\Cases;
use src\helpers\app\AppHelper;
use src\helpers\ProjectHashGenerator;
use src\repositories\cases\CasesRepository;
use src\repositories\NotFoundException;
use src\services\cases\CasesCommunicationService;
use Yii;
use frontend\controllers\FController;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use modules\product\src\abac\dto\RelatedProductQuoteAbacDto;
use modules\product\src\abac\RelatedProductQuoteAbacObject;
use modules\product\src\abac\dto\ProductQuoteChangeAbacDto;
use modules\product\src\abac\ProductQuoteChangeAbacObject;
use src\services\email\EmailMainService;
use src\exception\CreateModelException;
use src\exception\EmailNotSentException;

/**
 * Class ProductQuoteController
 *
 * @property ProductQuoteCloneService $productQuoteCloneService
 * @property EventDispatcher $eventDispatcher
 * @property ProductQuoteRepository $productQuoteRepository
 * @property CasesRepository $casesRepository
 * @property CasesCommunicationService $casesCommunicationService
 * @property ProductQuoteDataManageService $productQuoteDataManageService
 * @property ProductQuoteChangeRepository $productQuoteChangeRepository
 */
class ProductQuoteController extends FController
{
    private $productQuoteCloneService;
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;
    /**
     * @var ProductQuoteRepository
     */
    private $productQuoteRepository;
    /**
     * @var CasesRepository
     */
    private CasesRepository $casesRepository;
    /**
     * @var CasesCommunicationService
     */
    private CasesCommunicationService $casesCommunicationService;
    /**
     * @var ProductQuoteDataManageService
     */
    private ProductQuoteDataManageService $productQuoteDataManageService;
    private ProductQuoteChangeRepository $productQuoteChangeRepository;
    private EmailMainService $emailService;

    /**
     * ProductQuoteController constructor.
     * @param $id
     * @param $module
     * @param ProductQuoteCloneService $productQuoteCloneService
     * @param EventDispatcher $eventDispatcher
     * @param ProductQuoteRepository $productQuoteRepository
     * @param CasesRepository $casesRepository
     * @param CasesCommunicationService $casesCommunicationService
     * @param ProductQuoteDataManageService $productQuoteDataManageService
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        ProductQuoteCloneService $productQuoteCloneService,
        EventDispatcher $eventDispatcher,
        ProductQuoteRepository $productQuoteRepository,
        CasesRepository $casesRepository,
        CasesCommunicationService $casesCommunicationService,
        ProductQuoteDataManageService $productQuoteDataManageService,
        ProductQuoteChangeRepository $productQuoteChangeRepository,
        EmailMainService $emailService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->productQuoteCloneService = $productQuoteCloneService;
        $this->eventDispatcher = $eventDispatcher;
        $this->productQuoteRepository = $productQuoteRepository;
        $this->casesRepository = $casesRepository;
        $this->casesCommunicationService = $casesCommunicationService;
        $this->productQuoteDataManageService = $productQuoteDataManageService;
        $this->productQuoteChangeRepository = $productQuoteChangeRepository;
        $this->emailService = $emailService;
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete-ajax' => ['POST'],
                    'clone' => ['POST'],
                ],
            ],
            'access' => [
                'allowActions' => [
                    'preview-reprotection-quote-email',
                    'reprotection-quote-send-email',
                    'flight-reprotection-confirm',
                    'flight-reprotection-refund',
                    'origin-reprotection-quote-diff',
                    'set-recommended',
                    'ajax-decline-reprotection-quote'
                ]
            ]
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function actionClone(): Response
    {
        $productQuoteId = (int)Yii::$app->request->post('id');
        $productQuote = $this->findModel($productQuoteId);

        if (!$productQuote->pqProduct) {
            return $this->asJson(['error' => 'Error: not found relation Product']);
        }

        try {
            $clone = $this->productQuoteCloneService->clone($productQuote->pq_id, $productQuote->pqProduct->pr_id, Auth::id(), Auth::id());
            return $this->asJson(['message' => 'Successfully cloned product quote. New product quote (' . $clone->pq_id . ')']);
        } catch (\DomainException $e) {
            return $this->asJson(['error' => 'Error: ' . $e->getMessage()]);
        } catch (\Throwable $e) {
            Yii::error($e, 'ProductQuoteController:actionClone');
            return $this->asJson(['error' => 'Server error']);
        }
    }

    /**
     * @return array
     */
    public function actionDeleteAjax(): array
    {
        $id = (int)Yii::$app->request->post('id');

        Yii::$app->response->format = Response::FORMAT_JSON;

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model = $this->productQuoteRepository->find($id);
            $model->prepareRemove();
            $this->productQuoteRepository->remove($model);
            Notifications::pub(
                ['lead-' . $model->pqProduct->pr_lead_id],
                'removedQuote',
                ['data' => ['productId' => $model->pq_product_id]]
            );
            $transaction->commit();
        } catch (\Throwable $throwable) {
            $transaction->rollBack();
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }

        return ['message' => 'Successfully removed product quote (' . $model->pq_id . ')'];
    }

    public function actionPreviewVoluntaryOfferEmail()
    {
        $caseId = (int) Yii::$app->request->get('case_id');
        $originQuoteId = (int) Yii::$app->request->get('origin_quote_id');
        $orderId = (int) Yii::$app->request->get('order_id');
        $changeId = (int) Yii::$app->request->get('change_id');

        $form = new ChangeQuoteSendEmailForm();

        if (!$case = Cases::findOne((int)$caseId)) {
            $form->addError('general', 'Case Not Found');
        }

        if (!$order = Order::findOne((int)$orderId)) {
            throw new BadRequestHttpException('Order not found');
        }

        if (!$productQuoteChange = ProductQuoteChange::findOne($changeId)) {
            throw new BadRequestHttpException('ProductQuoteChange not found');
        }

        /** @abac new $pqcAbacDto, ProductQuoteChangeAbacObject::OBJ_PRODUCT_QUOTE_CHANGE, ProductQuoteChangeAbacObject::ACTION_SEND_OFFER_EXCHANGE_EMAIL, Act preview offer exchange email*/
        if (!Yii::$app->abac->can(new ProductQuoteChangeAbacDto($productQuoteChange), ProductQuoteChangeAbacObject::OBJ_PRODUCT_QUOTE_CHANGE, ProductQuoteChangeAbacObject::ACTION_SEND_OFFER_EXCHANGE_EMAIL)) {
            throw new ForbiddenHttpException('You do not have access to perform this action.');
        }

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $originalQuote = ProductQuote::findOne(['pq_id' => $originQuoteId]);
                if (!$originalQuote) {
                    throw new \RuntimeException('Original quote not found');
                }
                $locale = 'en-US';
                if ($case->client && $case->client->cl_locale) {
                    $locale = $case->client->cl_locale;
                }
                $emailData = $this->casesCommunicationService->getEmailData($case, Auth::user(), $locale);
                $emailData['change_gid'] = $productQuoteChange->pqc_gid;
                $emailData['original_quote'] = $originalQuote->serialize();

                $bookingId = $originalQuote->getBookingId();
                if (!$bookingId) {
                    Yii::warning([
                        'message' => 'ProjectHashGenerator::getHashByProjectId problem. Reason: BookingId is empty',
                        'data' => [
                            'data' => [
                                'booking_id' => $bookingId,
                                'product_quote_change_gid' => $productQuoteChange->pqc_gid,
                                'case_gid' => $case->cs_gid,
                                'product_quote_gid' => $originalQuote->pq_gid,
                            ]
                        ]
                    ], 'ProductQuoteController:actionPreviewVoluntaryOfferEmail:ProjectHashGenerator:getHashByProjectId');
                }
                $emailData['booking_hash_code'] = ProjectHashGenerator::getHashByProjectId($case->cs_project_id, $bookingId);
                if (!empty($emailData['original_quote']['data'])) {
                    ArrayHelper::remove($emailData['original_quote']['data'], 'fq_origin_search_data');
                }

                $userProjectParams = UserProjectParams::find()
                    ->andWhere(['upp_user_id' => Auth::id(), 'upp_project_id' => $case->cs_project_id])
                    ->withEmailList()
                    ->one();

                $emailFrom = ($userProjectParams) ? ($userProjectParams)->getEmail(true) : null;
                $emailTemplateType = null;
                $emailFromName = Auth::user()->nickname;

                if ($case->cs_project_id) {
                    $project = $case->project;
                    if ($project && $emailConfig = $project->getVoluntaryChangeEmailConfig()) {
                        $emailFrom = !empty($emailConfig['emailFrom'] ?? null) ? $emailConfig['emailFrom'] : $emailFrom;
                        $emailTemplateType = $emailConfig['templateTypeKey'] ?? '';
                        $emailFromName = !empty($emailConfig['emailFromName'] ?? null) ? $emailConfig['emailFromName'] : $emailFromName;
                    }
                }

                if (!$emailFrom) {
                    throw new \RuntimeException('No "email from" address available, please contact administrator.');
                }

                if (!$emailTemplateType) {
                    throw new \RuntimeException('Email template type is not set in project params');
                }
                $previewEmailResult = Yii::$app->comms->mailPreview($case->cs_project_id, $emailTemplateType, $emailFrom, $form->clientEmail, $emailData, $locale);
                if ($previewEmailResult['error']) {
                    $previewEmailResult['error'] = @Json::decode($previewEmailResult['error']);
                    $form->addError('general', 'Communication service error: ' . ($previewEmailResult['error']['name'] ?? '') . ' ( ' . ($previewEmailResult['error']['message']  ?? '') . ' )');
                } else {
                    $previewEmailForm = new VoluntaryChangeQuotePreviewEmailForm($previewEmailResult['data']);
                    $previewEmailForm->email_from_name = $emailFromName;
                    $previewEmailForm->changeId = $changeId;
                    $previewEmailForm->originQuoteId = $originQuoteId;

                    $emailTemplateType = EmailTemplateType::findOne(['etp_key' => $emailTemplateType]);
                    if ($emailTemplateType) {
                        $previewEmailForm->email_tpl_id = $emailTemplateType->etp_id;
                    }

                    return $this->renderAjax('partial/_voluntary_quote_preview_email', [
                        'previewEmailForm' => $previewEmailForm,
                    ]);
                }
            } catch (\DomainException | \RuntimeException | ForbiddenHttpException $e) {
                $form->addError('error', $e->getMessage());
            } catch (\Throwable $e) {
                Yii::error($e->getMessage(), 'ProductQuoteController::actionPreviewVoluntaryOfferEmail::Throwable');
                $form->addError('general', 'Internal Server Error');
            }
        }

        $form->caseId = $caseId;

        return $this->renderAjax('partial/_voluntary_quote_choose_client_email', [
            'form' => $form,
            'case' => $case,
            'order' => $order
        ]);
    }

    public function actionVoluntaryQuoteSendEmail()
    {
        $previewEmailForm = new VoluntaryChangeQuotePreviewEmailForm();

        if ($previewEmailForm->load(Yii::$app->request->post())) {
            if (!$case = Cases::findOne((int)$previewEmailForm->case_id)) {
                throw new BadRequestHttpException('Case Not Found');
            }

            if (!$originQuote = ProductQuote::findOne($previewEmailForm->originQuoteId)) {
                throw new BadRequestHttpException('OriginQuote Not Found');
            }

            if (!$productQuoteChange = ProductQuoteChange::findOne($previewEmailForm->changeId)) {
                throw new BadRequestHttpException('ProductQuoteChange Not Found');
            }

            /** @abac new $pqcAbacDto, ProductQuoteChangeAbacObject::OBJ_PRODUCT_QUOTE_CHANGE, ProductQuoteChangeAbacObject::ACTION_SEND_OFFER_EXCHANGE_EMAIL, Act send offer exchange email*/
            if (!Yii::$app->abac->can(new ProductQuoteChangeAbacDto($productQuoteChange), ProductQuoteChangeAbacObject::OBJ_PRODUCT_QUOTE_CHANGE, ProductQuoteChangeAbacObject::ACTION_SEND_OFFER_EXCHANGE_EMAIL)) {
                throw new ForbiddenHttpException('You do not have access to perform this action.');
            }

            if ($previewEmailForm->validate()) {
                try {
                    if (!$originQuote) {
                        throw new \RuntimeException('Origin quote not found');
                    }

                    $bookingId = !empty($case->cs_order_uid) ? $case->cs_order_uid : $originQuote->getLastBookingId();
                    if (empty($bookingId)) {
                        throw new BadRequestHttpException('Error: Booking ID missing.');
                    }

                    $mail = $this->emailService->createFromCase($previewEmailForm, $case);
                    $previewEmailForm->is_send = true;
                    $this->emailService->sendMail($mail);

                    $case->addEventLog(
                        null,
                        ($mail->getTemplateTypeName() ?? '') . ' email sent. By: ' . Auth::user()->username,
                        ['changeId' => $previewEmailForm->changeId],
                        CaseEventLog::CATEGORY_INFO
                    );

                    $productQuoteChange->statusToPending();
                    if (!$productQuoteChange->save()) {
                        Yii::warning(
                            'ProductQuoteChange saving failed: ' . $productQuoteChange->getErrorSummary(true)[0],
                            'ProductQuoteController::actionVoluntaryQuoteSendEmail::ProductQuoteChange::save'
                            );
                    }

                    try {
                        $whData = (new HybridWhData())->fillCollectedData(
                            HybridWhData::WH_TYPE_VOLUNTARY_CHANGE_UPDATE,
                            [
                                'booking_id' => $bookingId,
                                'product_quote_gid' => $originQuote->pq_gid,
                                'exchange_gid' => $productQuoteChange->pqc_gid,
                                'exchange_status' => ProductQuoteChangeStatus::getClientKeyStatusById($productQuoteChange->pqc_status_id),
                            ]
                            )->getCollectedData();

                            \Yii::info([
                                'type' => HybridWhData::WH_TYPE_VOLUNTARY_CHANGE_UPDATE,
                                'requestData' => $whData,
                                'ProductQuoteChangeStatus' => ProductQuoteChangeStatus::getName($productQuoteChange->pqc_status_id),
                            ], 'info\Webhook::OTA::ProductQuoteController:Request');

                            $responseData = \Yii::$app->hybrid->wh(
                                $case->cs_project_id,
                                HybridWhData::WH_TYPE_VOLUNTARY_CHANGE_UPDATE,
                                ['data' => $whData]
                                );

                            \Yii::info([
                                'type' => HybridWhData::WH_TYPE_VOLUNTARY_CHANGE_UPDATE,
                                'responseData' => $responseData,
                            ], 'info\Webhook::OTA::ProductQuoteController:Response');
                    } catch (\Throwable $throwable) {
                        $errorData = AppHelper::throwableLog($throwable);
                        $errorData['text'] = 'OTA site is not informed (VoluntaryQuoteSendEmail)';
                        $errorData['project_id'] = $case->cs_project_id;
                        $errorData['case_id'] = $case->cs_id;
                        Yii::warning($errorData, 'ProductQuoteController:actionVoluntaryQuoteSendEmail:Throwable');
                    }

                    return '<script>$("#modal-md").modal("hide");
                        createNotify("Success", "Success: <strong>Email Message</strong> is sent to <strong>' . $mail->getEmailTo(false) . '</strong>", "success");
                        if ($("#pjax-case-orders").length) {
                            pjaxReload({container: "#pjax-case-orders"});
                        }
                    </script>';
                } catch (CreateModelException $e) {
                    throw new \RuntimeException($e->getErrorSummary(false)[0]);
                } catch (EmailNotSentException $e) {
                    throw new \RuntimeException('Error: Email Message has not been sent to ' .  $e->getEmailTo(false));
                } catch (\DomainException | \RuntimeException $throwable) {
                    Yii::error(AppHelper::throwableLog($throwable), 'ProductQuoteController::actionVoluntaryQuoteSendEmail::Exception');
                    $previewEmailForm->addError('error', $throwable->getMessage());
                } catch (\Throwable $throwable) {
                    $previewEmailForm->addError('error', 'Internal Server Error');
                    Yii::error(AppHelper::throwableLog($throwable), 'ProductQuoteController::actionVoluntaryQuoteSendEmail::Throwable');
                }
            }
        }
        $previewEmailForm->case_id = $previewEmailForm->case_id ?: 0;

        return $this->renderAjax('partial/_voluntary_quote_preview_email', [
            'previewEmailForm' => $previewEmailForm
        ]);
    }

    public function actionPreviewReprotectionQuoteEmail()
    {
        $caseId = Yii::$app->request->get('case-id');
        $quoteId = Yii::$app->request->get('reprotection-quote-id');
        $orderId = Yii::$app->request->get('order-id');
        $pqcId = Yii::$app->request->get('pqc_id');

        $form = new ReprotectionQuoteSendEmailForm();

        if (!$case = Cases::findOne((int)$caseId)) {
            $form->addError('general', 'Case Not Found');
        }

        if (!$order = Order::findOne((int)$orderId)) {
            throw new BadRequestHttpException('Order not found');
        }

        $productQuoteChange = $this->productQuoteChangeRepository->find($pqcId);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $quote = $this->productQuoteRepository->find($form->quoteId);
                $originalQuote = ProductQuoteQuery::getOriginProductQuoteByReprotection($quote->pq_id);
                if (!$originalQuote) {
                    throw new \RuntimeException('Original quote not found');
                }

                $notClosedRefunds = ProductQuoteRefund::find()
                    ->andWhere(['pqr_product_quote_id' => $originalQuote->pq_id])
                    ->andWhere(['pqr_type_id' => ProductQuoteRefund::typeVoluntary()])
                    ->andWhere(['NOT IN', 'pqr_status_id', [ProductQuoteRefundStatus::DECLINED, ProductQuoteRefundStatus::CANCELED]])
                    ->exists();
                if ($notClosedRefunds) {
                    throw new \RuntimeException('Voluntary Refund - processing is not complete');
                }

                $locale = 'en-US';
                if ($case->client && $case->client->cl_locale) {
                    $locale = $case->client->cl_locale;
                }

                $relatedPrQtAbacDto = new RelatedProductQuoteAbacDto($quote);
                $relatedPrQtAbacDto->mapOrderAttributes($order);
                $relatedPrQtAbacDto->mapProductQuoteChangeAttributes($productQuoteChange);
                $relatedPrQtAbacDto->mapCaseAttributes($case);

                /** @abac $relatedPrQtAbacDto, RelatedProductQuoteAbacObject::OBJ_RELATED_PRODUCT_QUOTE, CRelatedProductQuoteAbacObject::ACTION_SEND_SC_EMAIL, ReProtection Quote preview email */
                if (!Yii::$app->abac->can($relatedPrQtAbacDto, RelatedProductQuoteAbacObject::OBJ_RELATED_PRODUCT_QUOTE, RelatedProductQuoteAbacObject::ACTION_SEND_SC_EMAIL)) {
                    throw new ForbiddenHttpException('You do not have access to perform this action', 403);
                }

                $emailData = $this->casesCommunicationService->getEmailData($case, Auth::user(), $locale);
                $emailData['reprotection_quote'] = $quote->serialize();
                $emailData['original_quote'] = $originalQuote->serialize();
                $bookingId = ArrayHelper::getValue($emailData, 'original_quote.data.flights.0.fqf_booking_id', '') ?? '';
                $emailData['booking_hash_code'] = ProjectHashGenerator::getHashByProjectId($case->cs_project_id, $bookingId);
                if (!empty($emailData['reprotection_quote']['data'])) {
                    ArrayHelper::remove($emailData['reprotection_quote']['data'], 'fq_origin_search_data');
                }
                if (!empty($emailData['original_quote']['data'])) {
                    ArrayHelper::remove($emailData['original_quote']['data'], 'fq_origin_search_data');
                }
                $emailFrom = Auth::user()->email;
                $emailTemplateType = null;
                $emailFromName = Auth::user()->nickname;

                if ($case->cs_project_id) {
                    $project = $case->project;
                    if ($project && $emailConfig = $project->getReprotectionQuoteEmailConfig()) {
                        $emailFrom = $emailConfig['emailFrom'] ?? '';
                        $emailTemplateType = $emailConfig['templateTypeKey'] ?? '';
                        $emailFromName = $emailConfig['emailFromName'] ?? $emailFromName;
                    }
                }

                if (!$emailFrom) {
                    throw new \RuntimeException('Agent not has assigned email; Setup in project settings object.case.reprotection_quote.emailFrom;');
                }

                if (!$emailTemplateType) {
                    throw new \RuntimeException('Email template type is not set in project params');
                }
                $previewEmailResult = Yii::$app->comms->mailPreview($case->cs_project_id, $emailTemplateType, $emailFrom, $form->clientEmail, $emailData, $locale);
                if ($previewEmailResult['error']) {
                    $previewEmailResult['error'] = @Json::decode($previewEmailResult['error']);
                    $form->addError('general', 'Communication service error: ' . ($previewEmailResult['error']['name'] ?? '') . ' ( ' . ($previewEmailResult['error']['message']  ?? '') . ' )');
                } else {
                    $previewEmailForm = new ReprotectionQuotePreviewEmailForm($previewEmailResult['data']);
                    $previewEmailForm->email_from_name = $emailFromName;
                    $previewEmailForm->productQuoteId = $quote->pq_id;
                    $previewEmailForm->orderId = $orderId;
                    $previewEmailForm->pqcId = $pqcId;

                    $emailTemplateType = EmailTemplateType::findOne(['etp_key' => $emailTemplateType]);
                    if ($emailTemplateType) {
                        $previewEmailForm->email_tpl_id = $emailTemplateType->etp_id;
                    }

                    return $this->renderAjax('partial/_reprotection_quote_preview_email', [
                        'previewEmailForm' => $previewEmailForm,
                    ]);
                }
            } catch (\DomainException | \RuntimeException | ForbiddenHttpException $e) {
                $form->addError('error', $e->getMessage());
            } catch (\Throwable $e) {
                Yii::error($e->getMessage(), 'ProductQuoteController::actionPreviewReprotectionQuoteEmail::Throwable');
                $form->addError('general', 'Internal Server Error');
            }
        }

        $form->caseId = $caseId;
        $form->quoteId = $quoteId;
        $form->orderId = $orderId;
        $form->pqcId = $pqcId;

        return $this->renderAjax('partial/_reprotection_quote_choose_client_email', [
            'form' => $form,
            'case' => $case,
            'order' => $order
        ]);
    }

    public function actionReprotectionQuoteSendEmail()
    {
        $previewEmailForm = new ReprotectionQuotePreviewEmailForm();

        if ($previewEmailForm->load(Yii::$app->request->post())) {
            if (!$case = Cases::findOne((int)$previewEmailForm->case_id)) {
                throw new BadRequestHttpException('Case Not Found');
            }

            if (!$order = Order::findOne($previewEmailForm->orderId)) {
                throw new BadRequestHttpException('Order not found');
            }

            $reprotectionQuote = $this->productQuoteRepository->find($previewEmailForm->productQuoteId);
            $originQuote = ProductQuoteQuery::getOriginProductQuoteByReprotection($reprotectionQuote->pq_id);
            $productQuoteChange = $this->productQuoteChangeRepository->find($previewEmailForm->pqcId);

            $relatedPrQtAbacDto = new RelatedProductQuoteAbacDto($reprotectionQuote);
            $relatedPrQtAbacDto->mapOrderAttributes($order);
            $relatedPrQtAbacDto->mapProductQuoteChangeAttributes($productQuoteChange);
            $relatedPrQtAbacDto->mapCaseAttributes($case);

            /** @abac $relatedPrQtAbacDto, RelatedProductQuoteAbacObject::OBJ_RELATED_PRODUCT_QUOTE, CRelatedProductQuoteAbacObject::ACTION_SEND_SC_EMAIL, ReProtection Quote preview email */
            if (!Yii::$app->abac->can($relatedPrQtAbacDto, RelatedProductQuoteAbacObject::OBJ_RELATED_PRODUCT_QUOTE, RelatedProductQuoteAbacObject::ACTION_SEND_SC_EMAIL)) {
                throw new ForbiddenHttpException('You do not have access to perform this action', 403);
            }

            if ($previewEmailForm->validate()) {
                try {
                    if (!$originQuote) {
                        throw new \RuntimeException('Origin quote not found');
                    }

                    $notClosedRefunds = ProductQuoteRefund::find()
                        ->andWhere(['pqr_product_quote_id' => $originQuote->pq_id])
                        ->andWhere(['pqr_type_id' => ProductQuoteRefund::typeVoluntary()])
                        ->andWhere(['NOT IN', 'pqr_status_id', [ProductQuoteRefundStatus::DECLINED, ProductQuoteRefundStatus::CANCELED]])
                        ->exists();
                    if ($notClosedRefunds) {
                        throw new \RuntimeException('Voluntary Refund - processing is not complete');
                    }

                    $mail = new Email();
                    $mail->e_project_id = $case->cs_project_id;
                    $mail->e_case_id = $case->cs_id;
                    if ($previewEmailForm->email_tpl_id) {
                        $mail->e_template_type_id = $previewEmailForm->email_tpl_id;
                    }
                    $mail->e_type_id = Email::TYPE_OUTBOX;
                    $mail->e_status_id = Email::STATUS_PENDING;
                    $mail->e_email_subject = $previewEmailForm->email_subject;
                    $mail->body_html = $previewEmailForm->email_message;
                    $mail->e_email_from = $previewEmailForm->email_from;

                    $mail->e_email_from_name = $previewEmailForm->email_from_name;
                    $mail->e_email_to_name = $previewEmailForm->email_to_name;

                    if ($previewEmailForm->language_id) {
                        $mail->e_language_id = $previewEmailForm->language_id;
                    }

                    $mail->e_email_to = $previewEmailForm->email_to;
                    //$mail->email_data = [];
                    $mail->e_created_dt = date('Y-m-d H:i:s');
                    $mail->e_created_user_id = Yii::$app->user->id;

                    if ($mail->save()) {
                        $mail->e_message_id = $mail->generateMessageId();
                        $mail->update();

                        $previewEmailForm->is_send = true;

                        $mailResponse = $mail->sendMail();

                        if (isset($mailResponse['error']) && $mailResponse['error']) {
                            throw new \RuntimeException('Error: Email Message has not been sent to ' .  $mail->e_email_to);
                        }

                        $case->addEventLog(null, ($mail->eTemplateType->etp_name ?? '') . ' email sent. By: ' . Auth::user()->username);

                        if ($productQuoteChange) {
                            $productQuoteChange->statusToPending();
                            if (!$productQuoteChange->save()) {
                                Yii::warning('ProductQuoteChange saving failed: ' . $productQuoteChange->getErrorSummary(true)[0], 'ProductQuoteController::actionReprotectionQuoteSendEmail::ProductQuoteChange::save');
                            }
                        }

                        $bookingId = $originQuote->getBookingId();
                        $data = [
                            'data' => [
                                'booking_id' => $bookingId,
                                'reprotection_quote_gid' => $reprotectionQuote->pq_gid,
                                'case_gid' => $case->cs_gid,
                                'product_quote_gid' => $originQuote->pq_gid,
                                'status' => ProductQuoteChangeStatus::getClientKeyStatusById($productQuoteChange->pqc_status_id),
                            ]
                        ];

                        if ($bookingId) {
                            try {
                                $hybridService = Yii::createObject(HybridService::class);
                                $hybridService->whReprotection($case->cs_project_id, $data);
                                $case->addEventLog(null, 'Request HybridService sent successfully');
                            } catch (\Throwable $throwable) {
                                $errorData = AppHelper::throwableLog($throwable);
                                $errorData['submessage'] = 'OTA site is not informed (hybridService->whReprotection)';
                                $errorData['project_id'] = $case->cs_project_id;
                                $errorData['case_id'] = $case->cs_id;

                                Yii::warning($errorData, 'ProductQuoteController:actionReprotectionQuoteSendEmail:Throwable');
                            }
                        } else {
                            Yii::warning([
                                    'message' => 'Error: WebHook hybridService "whReprotection" not sent. Reason: BookingId is empty',
                                    'data' => $data
                                ], 'ProductQuoteController:actionReprotectionQuoteSendEmail:HybridService:whReprotection');
                        }
                        return '<script>$("#modal-md").modal("hide"); createNotify("Success", "Success: <strong>Email Message</strong> is sent to <strong>' . $mail->e_email_to . '</strong>", "success")</script>';
                    }

                    throw new \RuntimeException($mail->getErrorSummary(false)[0]);
                } catch (\DomainException | \RuntimeException $throwable) {
                    Yii::error(AppHelper::throwableLog($throwable), 'ProductQuoteController::actionReprotectionQuoteSendEmail::DomainException|RuntimeException');
                    $previewEmailForm->addError('error', $throwable->getMessage());
                } catch (\Throwable $throwable) {
                    $previewEmailForm->addError('error', 'Internal Server Error');
                    Yii::error(AppHelper::throwableLog($throwable), 'ProductQuoteController::actionReprotectionQuoteSendEmail::Throwable');
                }
            }
        }
        $previewEmailForm->case_id = $previewEmailForm->case_id ?: 0;

        return $this->renderAjax('partial/_reprotection_quote_preview_email', [
            'previewEmailForm' => $previewEmailForm
        ]);
    }

    public function actionFlightReprotectionConfirm()
    {
        try {
            $quoteId = Yii::$app->request->post('quoteId');
            $caseId = Yii::$app->request->post('case_id');
            $orderId = Yii::$app->request->post('order_id');
            $pqcId = Yii::$app->request->post('pqc_id');

            if (!$quoteId) {
                throw new \Exception('Not found Quote ID');
            }
            if (!$caseId) {
                throw new \Exception('Not found Case ID');
            }
            if (!$orderId) {
                throw new \Exception('Not found Order ID');
            }
            if (!$pqcId) {
                throw new \Exception('Not found Product Quote Change ID');
            }

            $quote = Yii::createObject(ProductQuoteRepository::class)->find($quoteId);
            if (!$quote->isFlight()) {
                throw new \Exception('Quote is not flight quote.');
            }
            if (!$quote->flightQuote->isTypeReProtection()) {
                throw new \Exception('Quote is not reprotection.');
            }

            if (!$order = Order::findOne($orderId)) {
                throw new BadRequestHttpException('Order not found');
            }

            $productQuoteChange = $this->productQuoteChangeRepository->find($pqcId);
            $case = $this->casesRepository->find($caseId);

            $relatedProductQuoteAbacDto = new RelatedProductQuoteAbacDto($quote);
            $relatedProductQuoteAbacDto->mapOrderAttributes($order);
            $relatedProductQuoteAbacDto->mapProductQuoteChangeAttributes($productQuoteChange);
            $relatedProductQuoteAbacDto->mapCaseAttributes($case);

            /** @abac $relatedPrQtAbacDto, RelatedProductQuoteAbacObject::OBJ_RELATED_PRODUCT_QUOTE, RelatedProductQuoteAbacObject::ACTION_SET_CONFIRM, Act Flight ReProtection quote confirm */
            if (!Yii::$app->abac->can($relatedProductQuoteAbacDto, RelatedProductQuoteAbacObject::OBJ_RELATED_PRODUCT_QUOTE, RelatedProductQuoteAbacObject::ACTION_SET_CONFIRMED)) {
                throw new \Exception('You do not have access to perform this action.');
            }

            Yii::createObject(reprotectionDecision\confirm\Confirm::class)->handle($quote->pq_gid, Auth::id());

            return $this->asJson([
                'error' => false,
            ]);
        } catch (\Throwable $e) {
            return $this->asJson([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function actionFlightReprotectionRefund()
    {
        try {
            $quoteId = Yii::$app->request->post('quoteId');
            $caseId = Yii::$app->request->post('case_id');
            $orderId = Yii::$app->request->post('order_id');
            $pqcId = Yii::$app->request->post('pqc_id');

            if (!$quoteId) {
                throw new \Exception('Not found Quote ID');
            }
            if (!$caseId) {
                throw new \Exception('Not found Case ID');
            }
            if (!$orderId) {
                throw new \Exception('Not found Order ID');
            }
            if (!$pqcId) {
                throw new \Exception('Not found Product Quote Change ID');
            }

            $quote = Yii::createObject(ProductQuoteRepository::class)->find($quoteId);
            if (!$quote->isFlight()) {
                throw new \Exception('Quote is not flight quote.');
            }
            if (!$quote->flightQuote->isTypeReProtection()) {
                throw new \Exception('Quote is not reprotection.');
            }

            /*$productQuoteChange = Yii::createObject(ProductQuoteChangeRepository::class)->findParentRelated($quote);
            $case = $productQuoteChange->pqcCase;
            if (!$case) {
                throw new \DomainException('Not found related case.');
            }
            $caseAbacDto = new CasesAbacDto($case);
            if (!Yii::$app->abac->can($caseAbacDto, CasesAbacObject::ACT_FLIGHT_REPROTECTION_REFUND, CasesAbacObject::ACTION_ACCESS)) {
                throw new \Exception('You do not have access to perform this action');
            }*/

            if (!$order = Order::findOne($orderId)) {
                throw new BadRequestHttpException('Order not found');
            }

            $productQuoteChange = $this->productQuoteChangeRepository->find($pqcId);
            $case = $this->casesRepository->find($caseId);

            $relatedProductQuoteAbacDto = new RelatedProductQuoteAbacDto($quote);
            $relatedProductQuoteAbacDto->mapOrderAttributes($order);
            $relatedProductQuoteAbacDto->mapProductQuoteChangeAttributes($productQuoteChange);
            $relatedProductQuoteAbacDto->mapCaseAttributes($case);

            /** @abac $relatedPrQtAbacDto, RelatedProductQuoteAbacObject::OBJ_RELATED_PRODUCT_QUOTED, RelatedProductQuoteAbacObject::ACTION_SET_REFUNDED, Flight ReProtection quote refund */
            if (!Yii::$app->abac->can($relatedProductQuoteAbacDto, RelatedProductQuoteAbacObject::OBJ_RELATED_PRODUCT_QUOTE, RelatedProductQuoteAbacObject::ACTION_SET_REFUNDED)) {
                throw new \Exception('You do not have access to perform this action.');
            }

            $lastFlightQuoteFlightBookingId = FlightQuoteFlight::find()->select(['fqf_booking_id'])->andWhere(['fqf_fq_id' => $productQuoteChange->pqcPq->flightQuote->fq_id])->orderBy(['fqf_id' => SORT_DESC])->scalar();
            if (!$lastFlightQuoteFlightBookingId) {
                throw new \DomainException('Not found Booking Id. Quote ID: ' . $quote->pq_id);
            }

            Yii::createObject(reprotectionDecision\refund\Refund::class)->handle($lastFlightQuoteFlightBookingId, Auth::id());

            return $this->asJson([
                'error' => false,
            ]);
        } catch (\Throwable $e) {
            return $this->asJson([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function actionOriginReprotectionQuoteDiff()
    {
        $originQuoteId = Yii::$app->request->get('origin-quote-id', 0);
        $reprotectionQuoteId = Yii::$app->request->get('reprotection-quote-id', 0);

        $originQuote = $this->productQuoteRepository->find($originQuoteId);
        $reprotectionQuote = $this->productQuoteRepository->find($reprotectionQuoteId);

        return $this->renderAjax('partial/_diff_quotes', [
            'originQuote' => $originQuote,
            'reprotectionQuote' => $reprotectionQuote
        ]);
    }

    public function actionSetRecommended()
    {
        $changeQuoteId = Yii::$app->request->post('quoteId', 0);
        $caseId = Yii::$app->request->post('case_id', 0);
        $orderId = Yii::$app->request->post('order_id', 0);
        $pqcId = Yii::$app->request->post('pqc_id', 0);

        $result = [
            'error' => false,
            'message' => ''
        ];

        try {
            $changeQuote = $this->productQuoteRepository->find($changeQuoteId);

            if (!$order = Order::findOne($orderId)) {
                throw new BadRequestHttpException('Order not found');
            }

            $productQuoteChange = $this->productQuoteChangeRepository->find($pqcId);
            $case = $this->casesRepository->find($caseId);

            $relatedProductQuoteAbacDto = new RelatedProductQuoteAbacDto($changeQuote);
            $relatedProductQuoteAbacDto->mapOrderAttributes($order);
            $relatedProductQuoteAbacDto->mapProductQuoteChangeAttributes($productQuoteChange);
            $relatedProductQuoteAbacDto->mapCaseAttributes($case);

            /** @abac $relatedPrQtAbacDto, RelatedProductQuoteAbacObject::OBJ_RELATED_PRODUCT_QUOTE, RelatedProductQuoteAbacObject::ACTION_SET_REFUNDED, Act Flight ReProtection quote recommended */
            if (!Yii::$app->abac->can($relatedProductQuoteAbacDto, RelatedProductQuoteAbacObject::OBJ_RELATED_PRODUCT_QUOTE, RelatedProductQuoteAbacObject::ACTION_SET_RECOMMENDED)) {
                throw new \DomainException('You do not have access to perform this action');
            }

            if (!$originQuote = ProductQuoteQuery::getOriginProductQuoteByChangeQuote($changeQuote->pq_id)) {
                throw new NotFoundException('Origin Quote Not Found');
            }

            $this->productQuoteDataManageService->updateRecommendedChangeQuote($originQuote->pq_id, $changeQuote->pq_id);
        } catch (NotFoundException | \RuntimeException | \DomainException $e) {
            $result['error'] = true;
            $result['message'] = $e->getMessage();
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableLog($e, true), 'ProductQuoteController::actionSetRecommended::Throwable');
            $result['error'] = true;
            $result['message'] = 'Server Error';
        }

        return $this->asJson($result);
    }

    public function actionAjaxDeclineReprotectionQuote()
    {
        $changeQuoteId = Yii::$app->request->post('quoteId');
        $caseId = Yii::$app->request->post('case_id', 0);
        $orderId = Yii::$app->request->post('order_id', 0);
        $pqcId = Yii::$app->request->post('pqc_id', 0);

        if (!$changeQuote = ProductQuote::findOne($changeQuoteId)) {
            throw new BadRequestHttpException('Reprotection quote not found');
        }

        if (!$order = Order::findOne($orderId)) {
            throw new BadRequestHttpException('Order not found');
        }

        $productQuoteChange = $this->productQuoteChangeRepository->find($pqcId);
        $case = $this->casesRepository->find($caseId);

        $relatedProductQuoteAbacDto = new RelatedProductQuoteAbacDto($changeQuote);
        $relatedProductQuoteAbacDto->mapOrderAttributes($order);
        $relatedProductQuoteAbacDto->mapProductQuoteChangeAttributes($productQuoteChange);
        $relatedProductQuoteAbacDto->mapCaseAttributes($case);

        /** @abac $relatedPrQtAbacDto, RelatedProductQuoteAbacObject::OBJ_RELATED_PRODUCT_QUOTE, RelatedProductQuoteAbacObject::ACTION_SET_REFUNDED, Act ReProtection quote decline */
        if (!Yii::$app->abac->can($relatedProductQuoteAbacDto, RelatedProductQuoteAbacObject::OBJ_RELATED_PRODUCT_QUOTE, RelatedProductQuoteAbacObject::ACTION_SET_DECLINE)) {
            throw new ForbiddenHttpException('You do not have access to perform this action');
        }

        $result = [
            'error' => false,
            'message' => ''
        ];

        try {
            if (!$originQuote = ProductQuoteQuery::getOriginProductQuoteByChangeQuote($changeQuote->pq_id)) {
                throw new NotFoundException('Origin Quote Not Found');
            }

            $changeQuote->declined(Auth::id());
            $this->productQuoteRepository->save($changeQuote);

            $lastReProtectionQuote = ProductQuote::find()
                ->with('productQuoteDataRecommended')
                ->innerJoin(ProductQuoteRelation::tableName(), 'pqr_related_pq_id = pq_id and pqr_parent_pq_id = :parentQuoteId and pqr_type_id = :typeId', [
                    'typeId' => ProductQuoteRelation::TYPE_REPROTECTION,
                    'parentQuoteId' => $originQuote->pq_id
                ])
                ->andWhere(['!=', 'pq_status_id', ProductQuoteStatus::DECLINED])
                ->orderBy(['pq_id' => SORT_DESC])
                ->one();

            if ($lastReProtectionQuote) {
                $this->productQuoteDataManageService->updateRecommendedChangeQuote(
                    $originQuote->pq_id,
                    $lastReProtectionQuote->pq_id
                );
            }

            ProductQuoteChangeService::sendHybridDeclineNotification($productQuoteChange, $changeQuote, $case);
        } catch (\RuntimeException | \DomainException | NotFoundException $e) {
            $result['error'] = true;
            $result['message'] = $e->getMessage();
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableFormatter($e), 'ProductQuoteController:actionAjaxDeclineReprotectionQuote:Throwable');
            $result['error'] = true;
            $result['message'] = 'Internal Server Error';
        }

        return $this->asJson($result);
    }

    /**
     * @param $id
     * @return ProductQuote
     * @throws NotFoundHttpException
     */
    protected function findModel($id): ProductQuote
    {
        if (($model = ProductQuote::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
