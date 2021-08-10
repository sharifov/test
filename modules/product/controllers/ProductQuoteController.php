<?php

namespace modules\product\controllers;

use common\components\HybridService;
use common\models\Email;
use common\models\EmailTemplateType;
use common\models\Notifications;
use common\models\UserProjectParams;
use modules\cases\src\abac\CasesAbacObject;
use modules\cases\src\abac\dto\CasesAbacDto;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use modules\product\src\forms\ReprotectionQuotePreviewEmailForm;
use modules\product\src\forms\ReprotectionQuoteSendEmailForm;
use modules\product\src\services\productQuote\ProductQuoteCloneService;
use sales\auth\Auth;
use sales\dispatchers\EventDispatcher;
use sales\entities\cases\Cases;
use sales\exception\CheckRestrictionException;
use sales\helpers\app\AppHelper;
use sales\repositories\cases\CasesRepository;
use sales\services\cases\CasesCommunicationService;
use Yii;
use frontend\controllers\FController;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
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

        $form = new ReprotectionQuoteSendEmailForm();

        if (!$case = Cases::findOne((int)$caseId)) {
            $form->addError('general', 'Case Not Found');
        }

        $caseAbacDto = new CasesAbacDto($case);
        if (!Yii::$app->abac->can($caseAbacDto, CasesAbacObject::REPROTECTION_QUOTE_SEND_EMAIL, CasesAbacObject::ACTION_ACCESS)) {
            throw new ForbiddenHttpException('You do not have access to perform this action', 403);
        }

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $quote = $this->productQuoteRepository->find($form->quoteId);
                $emailData = $this->casesCommunicationService->getEmailData($case, Auth::user());
                $emailData['reprotection_quote'] = $quote->serialize();
                $emailData['order'] = $quote->pqOrder->serialize();
                $emailFrom = Auth::user()->email;

                if ($case->cs_project_id) {
                    $upp = UserProjectParams::find()->where(['upp_project_id' => $case->cs_project_id, 'upp_user_id' => Auth::id()])->withEmailList()->one();
                    if ($upp) {
                        $emailFrom = $upp->getEmail() ?: $emailFrom;
                    }
                }

                if (!$emailFrom) {
                    throw new \RuntimeException('Agent not has assigned email');
                }
                $previewEmailResult = Yii::$app->communication->mailPreview($case->cs_project_id, $form->emailTemplateType, $emailFrom, $form->clientEmail, $emailData);
                if ($previewEmailResult['error']) {
                    $previewEmailResult['error'] = @Json::decode($previewEmailResult['error']);
                    $form->addError('general', 'Communication service error: ' . ($result['error']['name'] ?? '') . ' ( ' . ($result['error']['message']  ?? '') . ' )');
                } else {
                    $previewEmailForm = new ReprotectionQuotePreviewEmailForm($previewEmailResult['data']);
                    $previewEmailForm->email_from_name = Auth::user()->nickname;
                    $previewEmailForm->productQuoteId = $quote->pq_id;

                    $emailTemplateType = EmailTemplateType::findOne(['etp_key' => $form->emailTemplateType]);
                    if ($emailTemplateType) {
                        $previewEmailForm->email_tpl_id = $emailTemplateType->etp_id;
                    }

                    return $this->renderAjax('partial/_reprotection_quote_preview_email', [
                        'previewEmailForm' => $previewEmailForm,
                    ]);
                }
            } catch (\DomainException | \RuntimeException $e) {
                Yii::error($e->getMessage(), 'ProductQuoteController::actionPreviewReprotectionQuoteEmail::DomainException|RuntimeException');
                $form->addError('error', $e->getMessage());
            } catch (\Throwable $e) {
                $form->addError('general', $e->getMessage());
            }
        }

        $form->caseId = $caseId;
        $form->quoteId = $quoteId;

        return $this->renderAjax('partial/_reprotection_quote_choose_client_email', [
            'form' => $form,
            'case' => $case
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
            if (!Yii::$app->abac->can($caseAbacDto, CasesAbacObject::REPROTECTION_QUOTE_SEND_EMAIL, CasesAbacObject::ACTION_ACCESS)) {
                throw new ForbiddenHttpException('You do not have access to perform this action', 403);
            }
            if ($previewEmailForm->validate()) {
                try {
                    $caseAbacDto = new CasesAbacDto($case);
                    if (!Yii::$app->abac->can($caseAbacDto, CasesAbacObject::REPROTECTION_QUOTE_SEND_EMAIL, CasesAbacObject::ACTION_ACCESS)) {
                        throw new ForbiddenHttpException('You do not have access to perform this action', 403);
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
                            $previewEmailForm->addError('error', 'Error: Email Message has not been sent to ' .  $mail->e_email_to);
                        }

                        try {
                            $hybridService = Yii::createObject(HybridService::class);
                            $data = [
                                'booking_id' => $case->cs_order_uid,
                                'reprotection_quote_gid' => $previewEmailForm->productQuoteId,
                                'case_gid' => $case->cs_gid,
                            ];
                            $hybridService->whReprotection($case->cs_project_id, $data);
                        } catch (\Throwable $throwable) {
                            Yii::warning('OTA site is not informed; Reason: ' . AppHelper::throwableFormatter($throwable), 'ProductQuoteController:actionReprotectionQuoteSendEmail:Throwable');
                        }

                        return '<script>$("#modal-md").modal("hide"); createNotify("Success", "Success: <strong>Email Message</strong> is sent to <strong>' . $mail->e_email_to . '</strong>", "success")</script>';
                    }

                    throw new \RuntimeException($mail->getErrorSummary(false)[0]);
                } catch (\DomainException | \RuntimeException $e) {
                    Yii::error($e->getMessage(), 'ProductQuoteController::actionReprotectionQuoteSendEmail::DomainException|RuntimeException');
                    $previewEmailForm->addError('error', $e->getMessage());
                } catch (\Throwable $e) {
                    $previewEmailForm->addError('error', 'Internal Server Error');
                    Yii::error($e->getMessage(), 'ProductQuoteController::actionReprotectionQuoteSendEmail::Throwable');
                }
            }
        }
        $previewEmailForm->case_id = $previewEmailForm->case_id ?: 0;

        return $this->renderAjax('partial/_reprotection_quote_preview_email', [
            'previewEmailForm' => $previewEmailForm
        ]);
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
