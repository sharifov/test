<?php

namespace frontend\controllers;

use common\models\Email;
use common\models\EmailTemplateType;
use frontend\models\CasePreviewEmailForm;
use sales\auth\Auth;
use sales\entities\cases\Cases;
use sales\helpers\app\AppHelper;
use sales\model\coupon\entity\coupon\CouponStatus;
use sales\model\coupon\entity\couponCase\CouponCase;
use sales\model\coupon\useCase\request\RequestCouponService;
use sales\model\coupon\useCase\request\RequestForm;
use sales\model\coupon\useCase\send\SendCouponsForm;
use sales\model\coupon\useCase\send\SendCouponsService;
use sales\repositories\cases\CasesRepository;
use Yii;
use sales\model\coupon\entity\coupon\Coupon;
use sales\model\coupon\entity\coupon\search\CouponSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

/**
 * Class CouponController
 *
 * @property RequestCouponService $requestCoupon
 * @property SendCouponsService $sendCouponsService
 * @property CasesRepository $casesRepository
 */
class CouponController extends FController
{
    private $requestCoupon;
    private $sendCouponsService;
    private $casesRepository;

    public function __construct($id, $module, RequestCouponService $requestCoupon, SendCouponsService $sendCouponsService, CasesRepository $casesRepository, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->requestCoupon = $requestCoupon;
        $this->sendCouponsService = $sendCouponsService;
        $this->casesRepository = $casesRepository;
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
                    'delete' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new CouponSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Coupon();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->c_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->c_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id): Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $id
     * @return Coupon
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Coupon
    {
        if (($model = Coupon::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @return string|Response
     */
    public function actionRequest()
    {
        $caseId = Yii::$app->request->get('caseId', '0');

        $form = new RequestForm($caseId, Auth::id());

        if ($form->load(Yii::$app->request->post())) {
            if ($form->validate()) {
                try {
                    $errors = $this->requestCoupon->request($form);
                    if ($errors) {
                        Yii::error(VarDumper::dumpAsString($errors), 'CouponController:actionRequest');
                        return $this->asJson(['success' => true, 'message' => 'Was some errors. Please contact to administrator.']);
                    }
                    return $this->asJson(['success' => true]);
                } catch (\DomainException $e) {
                    return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
                } catch (\Throwable $e) {
                    Yii::error($e, 'CouponController:' . __FUNCTION__);
                    return $this->asJson(['success' => false, 'message' => 'Server error']);
                }
            }
            return $this->asJson(\common\components\bootstrap4\activeForm\ActiveForm::formatError($form));
        }

        return $this->renderAjax('_request_form', [
            'model' => $form,
        ]);
    }

    public function actionPreview(): string
    {
        $form = new SendCouponsForm();

        if ($form->load(Yii::$app->request->post())) {
            if ($form->validate()) {
                try {
                    $case = $this->casesRepository->find($form->caseId);
                    $coupons = CouponCase::find()->getByCaseId($case ? $case->cs_id : 0)->all();
                    $result = $this->sendCouponsService->preview($form, $case, Auth::user());

                    if ($result['error']) {
                    } else {
                        $previewEmailForm = new CasePreviewEmailForm($result['data']);
                        $previewEmailForm->e_email_from_name = Auth::user()->nickname;
                        $previewEmailForm->coupon_list = json_encode($form->couponIds);

                        $emailTemplateType = EmailTemplateType::findOne(['etp_key' => $form->emailTemplateType]);
                        if ($emailTemplateType) {
                            $previewEmailForm->e_email_tpl_id = $emailTemplateType->etp_id;
                        }

                        return $this->render('/cases/coupons/view', [
                            'previewEmailForm' => $previewEmailForm,
                            'model' => $case,
                            'coupons' => $coupons
                        ]);
                    }
                } catch (\DomainException | \RuntimeException $e) {
                    Yii::error($e->getMessage(), 'CouponController::actionSend::DomainException|RuntimeException');
                    $form->addError('error', $e->getMessage());
                } catch (\Throwable $e) {
                    $form->addError('error', 'Internal Server Error');
                    Yii::error($e->getMessage(), 'CouponController::actionSend::Throwable');
                }
            }
        }

        $form->caseId = $form->caseId ?: 0;
        $case = Cases::findOne($form->caseId);
        $coupons = CouponCase::find()->getByCaseId($case ? $case->cs_id : 0)->all();

        return $this->render('/cases/coupons/view', [
            'model' => $case,
            'coupons' => $coupons,
            'sendCouponsForm' => $form
        ]);
    }

    public function actionSend(): string
    {
        $previewEmailForm = new CasePreviewEmailForm();

        if ($previewEmailForm->load(Yii::$app->request->post())) {
            if ($previewEmailForm->validate()) {
                try {
                    $case = $this->casesRepository->find($previewEmailForm->e_case_id);
                    $coupons = CouponCase::find()->getByCaseId($case ? $case->cs_id : 0)->all();

                    $mail = new Email();
                    $mail->e_project_id = $case->cs_project_id;
                    $mail->e_case_id = $case->cs_id;
                    if ($previewEmailForm->e_email_tpl_id) {
                        $mail->e_template_type_id = $previewEmailForm->e_email_tpl_id;
                    }
                    $mail->e_type_id = Email::TYPE_OUTBOX;
                    $mail->e_status_id = Email::STATUS_PENDING;
                    $mail->e_email_subject = $previewEmailForm->e_email_subject;
                    $mail->body_html = $previewEmailForm->e_email_message;
                    $mail->e_email_from = $previewEmailForm->e_email_from;

                    $mail->e_email_from_name = $previewEmailForm->e_email_from_name;
                    $mail->e_email_to_name = $previewEmailForm->e_email_to_name;

                    if ($previewEmailForm->e_language_id) {
                        $mail->e_language_id = $previewEmailForm->e_language_id;
                    }

                    $mail->e_email_to = $previewEmailForm->e_email_to;
                    //$mail->e_email_data = [];
                    $mail->e_created_dt = date('Y-m-d H:i:s');
                    $mail->e_created_user_id = Yii::$app->user->id;

                    if ($mail->save()) {
                        $mail->e_message_id = $mail->generateMessageId();
                        $mail->update();

                        $previewEmailForm->is_send = true;

                        $mailResponse = $mail->sendMail();

                        $selectedCoupons = json_decode($previewEmailForm->coupon_list, true);

                        foreach ($selectedCoupons as $couponId) {
                            $coupon = Coupon::findOne((int)$couponId);
                            if ($coupon) {
                                $coupon->c_status_id = CouponStatus::SEND;
                                $coupon->save();
                            }
                        }

                        if (isset($mailResponse['error']) && $mailResponse['error']) {
                            $previewEmailForm->addError('error', 'Error: Email Message has not been sent to ' .  $mail->e_email_to);
                        } else {
                            Yii::$app->session->setFlash('success', 'Success: <strong>Email Message</strong> is sent to <strong>' . $mail->e_email_to . '</strong>');

                            $form = new SendCouponsForm();
                            $form->caseId = $case->cs_id;
                            return $this->render('/cases/coupons/view', [
                                'model' => $case,
                                'coupons' => $coupons,
                                'sendCouponsForm' => $form
                            ]);
                        }
                    } else {
                        throw new \RuntimeException($mail->getErrorSummary(false)[0]);
                    }
                } catch (\DomainException | \RuntimeException $e) {
                    Yii::error($e->getMessage(), 'CouponController::actionSend::DomainException|RuntimeException');
                    $previewEmailForm->addError('error', $e->getMessage());
                } catch (\Throwable $e) {
                    $previewEmailForm->addError('error', 'Internal Server Error');
                    Yii::error($e->getMessage(), 'CouponController::actionSend::Throwable');
                }
            }
        }
        $previewEmailForm->e_case_id = $previewEmailForm->e_case_id ?: 0;
        $case = Cases::findOne($previewEmailForm->e_case_id);
        $coupons = CouponCase::find()->getByCaseId($case ? $case->cs_id : 0)->all();

        return $this->render('/cases/coupons/view', [
            'model' => $case,
            'coupons' => $coupons,
            'previewEmailForm' => $previewEmailForm
        ]);
    }
}
