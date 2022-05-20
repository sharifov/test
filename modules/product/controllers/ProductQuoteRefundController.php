<?php

namespace modules\product\controllers;

use common\components\hybrid\HybridWhData;
use common\models\Email;
use common\models\EmailTemplateType;
use common\models\UserProjectParams;
use modules\flight\src\useCases\voluntaryRefund\manualUpdate\VoluntaryRefundUpdateForm;
use modules\flight\src\useCases\voluntaryRefund\VoluntaryRefundService;
use modules\order\src\entities\order\Order;
use modules\product\src\abac\dto\ProductQuoteAbacDto;
use modules\product\src\abac\dto\ProductQuoteRefundAbacDto;
use modules\product\src\abac\ProductQuoteAbacObject;
use modules\product\src\abac\ProductQuoteRefundAbacObject;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use modules\product\src\entities\productQuoteObjectRefund\search\ProductQuoteObjectRefundSearch;
use modules\product\src\entities\productQuoteOptionRefund\search\ProductQuoteOptionRefundSearch;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundRepository;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundStatus;
use modules\product\src\forms\VoluntaryRefundPreviewEmailForm;
use modules\product\src\forms\VoluntaryRefundSendEmailForm;
use src\auth\Auth;
use src\entities\cases\CaseEventLog;
use src\entities\cases\Cases;
use src\forms\CompositeFormHelper;
use src\helpers\app\AppHelper;
use src\helpers\DateHelper;
use src\helpers\ProjectHashGenerator;
use src\repositories\NotFoundException;
use src\services\cases\CasesCommunicationService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

/**
 * Class FlightQuoteRefundController
 * @package modules\flight\controllers
 *
 * @property-read ProductQuoteRefundRepository $productQuoteRefundRepository
 * @property-read ProductQuoteRepository $productQuoteRepository
 * @property-read CasesCommunicationService $casesCommunicationService
 * @property-read VoluntaryRefundService $voluntaryRefundService
 */
class ProductQuoteRefundController extends \frontend\controllers\FController
{
    private ProductQuoteRefundRepository $productQuoteRefundRepository;
    private ProductQuoteRepository $productQuoteRepository;
    private CasesCommunicationService $casesCommunicationService;
    private VoluntaryRefundService $voluntaryRefundService;

    public function __construct(
        $id,
        $module,
        ProductQuoteRefundRepository $productQuoteRefundRepository,
        ProductQuoteRepository $productQuoteRepository,
        CasesCommunicationService $casesCommunicationService,
        VoluntaryRefundService $voluntaryRefundService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->productQuoteRefundRepository = $productQuoteRefundRepository;
        $this->productQuoteRepository = $productQuoteRepository;
        $this->casesCommunicationService = $casesCommunicationService;
        $this->voluntaryRefundService = $voluntaryRefundService;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'allowActions' => [
                    'ajax-view-details',
                    'preview-refund-offer-email',
                    'voluntary-refund-send-email',
                    'edit-refund'
                ]
            ]
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function actionAjaxViewDetails()
    {
        $productQuoteId = Yii::$app->request->get('id');

        $productQuoteRefund = $this->productQuoteRefundRepository->find($productQuoteId);
        /** @abac $pqrAbacDto, ProductQuoteRefundAbacObject::OBJ_PRODUCT_QUOTE_REFUND, ProductQuoteRefundAbacObject::ACTION_ACCESS_DETAILS, Product quote refund view details */
        if (!Yii::$app->abac->can(new ProductQuoteRefundAbacDto($productQuoteRefund), ProductQuoteRefundAbacObject::OBJ_PRODUCT_QUOTE_REFUND, ProductQuoteRefundAbacObject::ACTION_ACCESS_DETAILS)) {
            throw new ForbiddenHttpException('Access denied');
        }

        $dataProvider = new ActiveDataProvider();
        $dataProvider->setModels([$productQuoteRefund]);
        $dataProvider->pagination = false;

        $productQuoteObjectRefundSearch = new ProductQuoteObjectRefundSearch();
        $objectsRefundProvider = $productQuoteObjectRefundSearch->search([$productQuoteObjectRefundSearch->formName() => [
            'pqor_product_quote_refund_id' => $productQuoteId
        ]]);

        $productQuoteOptionsRefundSearch = new ProductQuoteOptionRefundSearch();
        $optionsRefundProvider = $productQuoteOptionsRefundSearch->search([$productQuoteOptionsRefundSearch->formName() => [
            'pqor_product_quote_refund_id' => $productQuoteId
        ]]);

        return $this->renderAjax('partial/_quote_view_details', [
            'dataProvider' => $dataProvider,
            'objectsRefundProvider' => $objectsRefundProvider,
            'optionsRefundProvider' => $optionsRefundProvider
        ]);
    }

    public function actionPreviewRefundOfferEmail()
    {
        $caseId = Yii::$app->request->get('case-id');
        $productQuoteRefundId = Yii::$app->request->get('product-quote-refund-id');
        $originQuoteId = Yii::$app->request->get('origin-quote-id');
        $orderId = Yii::$app->request->get('order-id');

        $form = new VoluntaryRefundSendEmailForm();

        if (!$case = Cases::findOne((int)$caseId)) {
            $form->addError('general', 'Case Not Found');
        }

        if (!$order = Order::findOne((int)$orderId)) {
            throw new BadRequestHttpException('Order not found');
        }

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $originalQuote = $this->productQuoteRepository->find($form->originProductQuoteId);
                $productQuoteRefund = $this->productQuoteRefundRepository->find($form->productQuoteRefundId);

                /** @abac $pqrAbacDto, ProductQuoteRefundAbacObject::OBJ_PRODUCT_QUOTE_REFUND, ProductQuoteRefundAbacObject::ACTION_ACCESS_DETAILS, Product quote refund send email */
                if (!Yii::$app->abac->can(new ProductQuoteRefundAbacDto($productQuoteRefund), ProductQuoteRefundAbacObject::OBJ_PRODUCT_QUOTE_REFUND, ProductQuoteRefundAbacObject::ACTION_SEND_VOL_REFUND_EMAIL)) {
                    throw new ForbiddenHttpException('Access denied');
                }

                $emailData = $this->casesCommunicationService->getEmailData($case, Auth::user());
                $emailData['original_quote'] = $originalQuote->serialize();
                $emailData['refund'] = $productQuoteRefund->serialize();
                $emailData['refundData'] = $productQuoteRefund->orderRefund->serialize() ?? [];
                $bookingId = ArrayHelper::getValue($emailData, 'original_quote.data.flights.0.fqf_booking_id', '') ?? '';
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
                    if ($project && $emailConfig = $project->getVoluntaryRefundEmailConfig()) {
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
                $previewEmailResult = Yii::$app->communication->mailPreview($case->cs_project_id, $emailTemplateType, $emailFrom, $form->clientEmail, $emailData);
                if ($previewEmailResult['error']) {
                    $previewEmailResult['error'] = @Json::decode($previewEmailResult['error']);
                    $form->addError('general', 'Communication service error: ' . ($previewEmailResult['error']['name'] ?? '') . ' ( ' . ($previewEmailResult['error']['message']  ?? '') . ' )');
                } else {
                    $previewEmailForm = new VoluntaryRefundPreviewEmailForm($previewEmailResult['data']);
                    $previewEmailForm->email_from_name = $emailFromName;
                    $previewEmailForm->productQuoteId = $originalQuote->pq_id;
                    $previewEmailForm->productQuoteRefundId = $productQuoteRefund->pqr_id;
                    $previewEmailForm->bookingId = $bookingId;

                    $emailTemplateType = EmailTemplateType::findOne(['etp_key' => $emailTemplateType]);
                    if ($emailTemplateType) {
                        $previewEmailForm->email_tpl_id = $emailTemplateType->etp_id;
                    }

                    return $this->renderAjax('partial/_voluntary_refund_preview_email', [
                        'previewEmailForm' => $previewEmailForm,
                    ]);
                }
            } catch (\DomainException | \RuntimeException | NotFoundException $e) {
                $form->addError('error', $e->getMessage());
            } catch (\Throwable $e) {
                Yii::error($e->getMessage(), 'ProductQuoteRefundController::actionPreviewRefundOfferEmail::Throwable');
                $form->addError('general', 'Internal Server Error');
            }
        }

        $form->caseId = $caseId;
        $form->originProductQuoteId = $originQuoteId;
        $form->productQuoteRefundId = $productQuoteRefundId;

        return $this->renderAjax('partial/_voluntary_refund_choose_client_email', [
            'form' => $form,
            'case' => $case,
            'order' => $order
        ]);
    }

    public function actionVoluntaryRefundSendEmail()
    {
        $previewEmailForm = new VoluntaryRefundPreviewEmailForm();

        if ($previewEmailForm->load(Yii::$app->request->post())) {
            if (!$case = Cases::findOne((int)$previewEmailForm->case_id)) {
                throw new BadRequestHttpException('Case Not Found');
            }


            if ($previewEmailForm->validate()) {
                try {
                    $originQuote = $this->productQuoteRepository->find($previewEmailForm->productQuoteId);
                    $productQuoteRefund = $this->productQuoteRefundRepository->find($previewEmailForm->productQuoteRefundId);

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

                        $case->addEventLog(CaseEventLog::VOLUNTARY_REFUND_EMAIL_SEND, ($mail->eTemplateType->etp_name ?? '') . ' email sent. By: ' . Auth::user()->username, [], CaseEventLog::CATEGORY_INFO);

                        $productQuoteRefund->pending();
                        $this->productQuoteRefundRepository->save($productQuoteRefund);

                        try {
                            $whData = HybridWhData::getData(HybridWhData::WH_TYPE_VOLUNTARY_REFUND_UPDATE);
                            $whData['booking_id'] = $previewEmailForm->bookingId;
                            $whData['product_quote_gid'] = $originQuote->pq_gid;
                            $whData['refund_gid'] = $productQuoteRefund->pqr_gid;
                            $whData['refund_order_id'] = $productQuoteRefund->pqr_cid;
                            $whData['refund_status'] = ProductQuoteRefundStatus::getClientKeyStatusById($productQuoteRefund->pqr_status_id);
                            \Yii::$app->hybrid->wh($case->cs_project_id, HybridWhData::WH_TYPE_VOLUNTARY_REFUND_UPDATE, ['data' => $whData]);
                            $case->addEventLog(CaseEventLog::VOLUNTARY_REFUND_WH_SEND_OTA, 'WH to HybridService sent successfully', $whData, CaseEventLog::CATEGORY_DEBUG);
                        } catch (\Throwable $throwable) {
                            // $errorData = [];
                            $errorData = AppHelper::throwableLog($throwable);
                            $errorData['description'] = 'OTA site is not informed (hybridService->whVoluntaryRefund)';
                            $errorData['project_id'] = $case->cs_project_id;
                            $errorData['case_id'] = $case->cs_id;
                            $case->addEventLog(CaseEventLog::VOLUNTARY_REFUND_WH_SEND_OTA, 'WH to HybridService failed', $errorData, CaseEventLog::CATEGORY_ERROR);
                            Yii::warning($errorData, 'ProductQuoteRefundController:actionVoluntaryRefundSendEmail:Throwable');
                        }

                        return '<script>$("#modal-md").modal("hide"); createNotify("Success", "Success: <strong>Email Message</strong> is sent to <strong>' . $mail->e_email_to . '</strong>", "success")</script>';
                    }

                    throw new \RuntimeException($mail->getErrorSummary(false)[0]);
                } catch (\DomainException | \RuntimeException $throwable) {
                    Yii::error(AppHelper::throwableLog($throwable), 'ProductQuoteRefundController::actionVoluntaryRefundSendEmail::DomainException|RuntimeException');
                    $previewEmailForm->addError('error', $throwable->getMessage());
                } catch (\Throwable $throwable) {
                    $previewEmailForm->addError('error', 'Internal Server Error');
                    Yii::error(AppHelper::throwableLog($throwable), 'ProductQuoteRefundController::actionVoluntaryRefundSendEmail::Throwable');
                }
            }
        }
        $previewEmailForm->case_id = $previewEmailForm->case_id ?: 0;

        return $this->renderAjax('partial/_voluntary_refund_preview_email', [
            'previewEmailForm' => $previewEmailForm
        ]);
    }

    public function actionEditRefund()
    {
        $productQuoteRefundId = Yii::$app->request->get('product-quote-refund-id');

        $productQuoteRefund = $this->productQuoteRefundRepository->find($productQuoteRefundId);

        /** @abac new ProductQuoteRefundAbacDto($model), ProductQuoteRefundAbacObject::OBJ_PRODUCT_QUOTE_REFUND, ProductQuoteRefundAbacObject::ACTION_UPDATE, Update Voluntary Quote Refund */
        if (!Yii::$app->abac->can(new ProductQuoteRefundAbacDto($productQuoteRefund), ProductQuoteRefundAbacObject::OBJ_PRODUCT_QUOTE_REFUND, ProductQuoteRefundAbacObject::ACTION_UPDATE)) {
            throw new ForbiddenHttpException('Access denied');
        }

        if (Yii::$app->request->isPjax) {
            $form = new VoluntaryRefundUpdateForm($productQuoteRefund);
            $data = CompositeFormHelper::prepareDataForMultiInput(
                Yii::$app->request->post(),
                'VoluntaryRefundUpdateForm',
                ['tickets' => 'TicketForm']
            );
            if ($form->load($data['post']) && $form->validate()) {
                try {
                    $this->voluntaryRefundService->updateManual($productQuoteRefund, $form);

                    return "<script>$('#modal-lg').modal('hide');createNotify('Success', 'Refund updated', 'success');pjaxReload({container: '#pjax-case-orders'})</script>";
                } catch (NotFoundException | \RuntimeException | \DomainException $e) {
                    $form->addError('general', $e->getMessage());
                } catch (\Throwable $e) {
                    $form->addError('general', 'Server error, check system logs');
                    Yii::error(AppHelper::throwableLog($e, true), 'ProductQuoteRefundController::actionEditRefund::Throwable');
                }
            }
            return $this->renderAjax('partial/_voluntary_refund_update', [
                'refundForm' => $form,
                'message' => '',
                'errors' => []
            ]);
        }

        if (Yii::$app->request->isAjax) {
            $form = new VoluntaryRefundUpdateForm($productQuoteRefund);
            if ($productQuoteRefund->pqr_expiration_dt) {
                $form->setExpirationDate(DateHelper::toFormat($productQuoteRefund->pqr_expiration_dt));
            }
            return $this->renderAjax('partial/_voluntary_refund_update', [
                'refundForm' => $form,
                'message' => '',
                'errors' => []
            ]);
        }
        throw new BadRequestHttpException('Method not allowed');
    }
}
