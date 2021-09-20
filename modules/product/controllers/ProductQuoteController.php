<?php

namespace modules\product\controllers;

use common\components\HybridService;
use common\models\Email;
use common\models\EmailTemplateType;
use common\models\Notifications;
use common\models\UserProjectParams;
use modules\cases\src\abac\CasesAbacObject;
use modules\cases\src\abac\dto\CasesAbacDto;
use modules\flight\models\FlightQuoteFlight;
use modules\flight\src\useCases\reprotectionDecision;
use modules\order\src\entities\order\Order;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteQuery;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeRepository;
use modules\product\src\entities\productQuoteData\ProductQuoteData;
use modules\product\src\forms\ReprotectionQuotePreviewEmailForm;
use modules\product\src\forms\ReprotectionQuoteSendEmailForm;
use modules\product\src\services\productQuote\ProductQuoteCloneService;
use sales\auth\Auth;
use sales\dispatchers\EventDispatcher;
use sales\entities\cases\CaseEventLog;
use sales\entities\cases\Cases;
use sales\exception\CheckRestrictionException;
use sales\helpers\app\AppHelper;
use sales\helpers\ProjectHashGenerator;
use sales\repositories\cases\CasesRepository;
use sales\repositories\NotFoundException;
use sales\services\cases\CasesCommunicationService;
use webapi\src\response\behaviors\RequestBehavior;
use Yii;
use frontend\controllers\FController;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class ProductQuoteController
 *
 * @property ProductQuoteCloneService $productQuoteCloneService
 * @property EventDispatcher $eventDispatcher
 * @property ProductQuoteRepository $productQuoteRepository
 * @property CasesRepository $casesRepository
 * @property CasesCommunicationService $casesCommunicationService
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
     * ProductQuoteController constructor.
     * @param $id
     * @param $module
     * @param ProductQuoteCloneService $productQuoteCloneService
     * @param EventDispatcher $eventDispatcher
     * @param ProductQuoteRepository $productQuoteRepository
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
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->productQuoteCloneService = $productQuoteCloneService;
        $this->eventDispatcher = $eventDispatcher;
        $this->productQuoteRepository = $productQuoteRepository;
        $this->casesRepository = $casesRepository;
        $this->casesCommunicationService = $casesCommunicationService;
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
                    'set-recommended'
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

    public function actionPreviewReprotectionQuoteEmail()
    {
        $caseId = Yii::$app->request->get('case-id');
        $quoteId = Yii::$app->request->get('reprotection-quote-id');
        $orderId = Yii::$app->request->get('order-id');

        $form = new ReprotectionQuoteSendEmailForm();

        if (!$case = Cases::findOne((int)$caseId)) {
            $form->addError('general', 'Case Not Found');
        }

        $caseAbacDto = new CasesAbacDto($case);
        if (!Yii::$app->abac->can($caseAbacDto, CasesAbacObject::ACT_REPROTECTION_QUOTE_SEND_EMAIL, CasesAbacObject::ACTION_ACCESS)) {
            throw new ForbiddenHttpException('You do not have access to perform this action', 403);
        }

        if (!$order = Order::findOne((int)$orderId)) {
            throw new BadRequestHttpException('Order not found');
        }

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $quote = $this->productQuoteRepository->find($form->quoteId);
                $originalQuote = ProductQuoteQuery::getOriginProductQuoteByReprotection($quote->pq_id);
                if (!$originalQuote) {
                    throw new \RuntimeException('Original quote not found');
                }
                $emailData = $this->casesCommunicationService->getEmailData($case, Auth::user());
                $emailData['reprotection_quote'] = $quote->serialize();
                $emailData['original_quote'] = $originalQuote->serialize();
                $bookingId = ArrayHelper::getValue($emailData, 'original_quote.data.flights.0.fqf_booking_id', '');
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
                    throw new \RuntimeException('Agent not has assigned email');
                }

                if (!$emailTemplateType) {
                    throw new \RuntimeException('Email template type is not set in project params');
                }
                $previewEmailResult = Yii::$app->communication->mailPreview($case->cs_project_id, $emailTemplateType, $emailFrom, $form->clientEmail, $emailData);
                if ($previewEmailResult['error']) {
                    $previewEmailResult['error'] = @Json::decode($previewEmailResult['error']);
                    $form->addError('general', 'Communication service error: ' . ($previewEmailResult['error']['name'] ?? '') . ' ( ' . ($previewEmailResult['error']['message']  ?? '') . ' )');
                } else {
                    $previewEmailForm = new ReprotectionQuotePreviewEmailForm($previewEmailResult['data']);
                    $previewEmailForm->email_from_name = $emailFromName;
                    $previewEmailForm->productQuoteId = $quote->pq_id;

                    $emailTemplateType = EmailTemplateType::findOne(['etp_key' => $emailTemplateType]);
                    if ($emailTemplateType) {
                        $previewEmailForm->email_tpl_id = $emailTemplateType->etp_id;
                    }

                    return $this->renderAjax('partial/_reprotection_quote_preview_email', [
                        'previewEmailForm' => $previewEmailForm,
                    ]);
                }
            } catch (\DomainException | \RuntimeException $e) {
                $form->addError('error', $e->getMessage());
            } catch (\Throwable $e) {
                Yii::error($e->getMessage(), 'ProductQuoteController::actionPreviewReprotectionQuoteEmail::Throwable');
                $form->addError('general', 'Internal Server Error');
            }
        }

        $form->caseId = $caseId;
        $form->quoteId = $quoteId;

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

            $caseAbacDto = new CasesAbacDto($case);
            if (!Yii::$app->abac->can($caseAbacDto, CasesAbacObject::ACT_REPROTECTION_QUOTE_SEND_EMAIL, CasesAbacObject::ACTION_ACCESS)) {
                throw new ForbiddenHttpException('You do not have access to perform this action', 403);
            }
            if ($previewEmailForm->validate()) {
                try {
                    $caseAbacDto = new CasesAbacDto($case);
                    if (!Yii::$app->abac->can($caseAbacDto, CasesAbacObject::ACT_REPROTECTION_QUOTE_SEND_EMAIL, CasesAbacObject::ACTION_ACCESS)) {
                        throw new ForbiddenHttpException('You do not have access to perform this action', 403);
                    }

                    $reprotectionQuote = $this->productQuoteRepository->find($previewEmailForm->productQuoteId);

                    $originQuote = ProductQuoteQuery::getOriginProductQuoteByReprotection($reprotectionQuote->pq_id);
                    if (!$originQuote) {
                        throw new \RuntimeException('Origin quote not found');
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

                        $case->addEventLog(null, $mail->eTemplateType->etp_name . ' email sent. By: ' . Auth::user()->username);

                        $productQuoteChange = ProductQuoteChange::find()->byProductQuote($originQuote->pq_id)->byCaseId($case->cs_id)->one();
                        if ($productQuoteChange) {
                            $productQuoteChange->decisionPending();
                            if (!$productQuoteChange->save()) {
                                Yii::warning('ProductQuoteChange saving failed: ' . $productQuoteChange->getErrorSummary(true)[0], 'ProductQuoteController::actionReprotectionQuoteSendEmail::ProductQuoteChange::save');
                            }
                        }

                        try {
                            $hybridService = Yii::createObject(HybridService::class);
                            $data = [
                                'data' => [
                                    'booking_id' => $case->cs_order_uid,
                                    'reprotection_quote_gid' => $reprotectionQuote->pq_gid,
                                    'case_gid' => $case->cs_gid,
                                    'product_quote_gid' => $originQuote->pq_gid,
                                ]
                            ];
                            $hybridService->whReprotection($case->cs_project_id, $data);
                            $case->addEventLog(null, 'Request HybridService sent successfully');
                        } catch (\Throwable $throwable) {
                            $errorData = [];
                            $errorData['message'] = 'OTA site is not informed (hybridService->whReprotection)';
                            $errorData['project_id'] = $case->cs_project_id;
                            $errorData['case_id'] = $case->cs_id;
                            $errorData['throwable'] = AppHelper::throwableLog($throwable);

                            Yii::warning($errorData, 'ProductQuoteController:actionReprotectionQuoteSendEmail:Throwable');
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
            if (!$quoteId) {
                throw new \Exception('Not found Quote ID');
            }

            $quote = Yii::createObject(ProductQuoteRepository::class)->find($quoteId);
            if (!$quote->isFlight()) {
                throw new \Exception('Quote is not flight quote.');
            }
            if (!$quote->flightQuote->isTypeReProtection()) {
                throw new \Exception('Quote is not reprotection.');
            }
            $productQuoteChange = Yii::createObject(ProductQuoteChangeRepository::class)->findParentRelated($quote);
            $case = $productQuoteChange->pqcCase;
            if (!$case) {
                throw new \DomainException('Not found related case.');
            }
            $caseAbacDto = new CasesAbacDto($case);
            if (!Yii::$app->abac->can($caseAbacDto, CasesAbacObject::ACT_FLIGHT_REPROTECTION_CONFIRM, CasesAbacObject::ACTION_ACCESS)) {
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
            if (!$quoteId) {
                throw new \Exception('Not found Quote ID');
            }

            $quote = Yii::createObject(ProductQuoteRepository::class)->find($quoteId);
            if (!$quote->isFlight()) {
                throw new \Exception('Quote is not flight quote.');
            }
            if (!$quote->flightQuote->isTypeReProtection()) {
                throw new \Exception('Quote is not reprotection.');
            }

            $productQuoteChange = Yii::createObject(ProductQuoteChangeRepository::class)->findParentRelated($quote);
            $case = $productQuoteChange->pqcCase;
            if (!$case) {
                throw new \DomainException('Not found related case.');
            }
            $caseAbacDto = new CasesAbacDto($case);
            if (!Yii::$app->abac->can($caseAbacDto, CasesAbacObject::ACT_FLIGHT_REPROTECTION_REFUND, CasesAbacObject::ACTION_ACCESS)) {
                throw new \Exception('You do not have access to perform this action');
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
        $reprotectionQuoteId = Yii::$app->request->post('quoteId', 0);

        $result = [
            'error' => false,
            'message' => ''
        ];

        try {
            if (!Yii::$app->abac->can(null, CasesAbacObject::ACT_VIEW_SET_RECOMMENDED_REPROTECTION_QUOTE, CasesAbacObject::ACTION_ACCESS)) {
                throw new \DomainException('You do not have access to perform this action');
            }

            $reprotectionQuote = $this->productQuoteRepository->find($reprotectionQuoteId);

            if (!$originQuote = ProductQuoteQuery::getOriginProductQuoteByReprotection($reprotectionQuote->pq_id)) {
                throw new NotFoundException('Origin Quote Not Found');
            }

            $reprotectionQuotes = ProductQuoteQuery::getReprotectionQuotesByOriginQuote($originQuote->pq_id);

            foreach ($reprotectionQuotes as $reprotectionQuoteRecommended) {
                if ($reprotectionQuoteRecommended->productQuoteDataRecommended && !$reprotectionQuoteRecommended->productQuoteDataRecommended->delete()) {
                    throw new \RuntimeException('Unable to remove recommended reprotection quote flag');
                }
            }

            $recommendedQuote = ProductQuoteData::createRecommended($reprotectionQuote->pq_id);
            if (!$recommendedQuote->save()) {
                throw new \RuntimeException('Unable to set recommended reprotection quote flag: ' . $recommendedQuote->getErrorSummary(true)[0]);
            }
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
