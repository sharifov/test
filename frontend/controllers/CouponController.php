<?php

namespace frontend\controllers;

use common\models\Email;
use common\models\EmailTemplateType;
use frontend\models\CaseCouponPreviewEmailForm;
use src\auth\Auth;
use src\entities\cases\Cases;
use src\exception\CreateModelException;
use src\exception\EmailNotSentException;
use src\model\coupon\entity\coupon\Coupon;
use src\model\coupon\entity\coupon\CouponStatus;
use src\model\coupon\entity\coupon\search\CouponSearch;
use src\model\coupon\entity\couponCase\CouponCase;
use src\model\coupon\entity\couponClient\CouponClient;
use src\model\coupon\entity\couponClient\repository\CouponClientRepository;
use src\model\coupon\entity\couponSend\CouponSend;
use src\model\coupon\entity\couponSend\repository\CouponSendRepository;
use src\model\coupon\useCase\request\RequestCouponService;
use src\model\coupon\useCase\request\RequestForm;
use src\model\coupon\useCase\send\SendCouponsForm;
use src\model\coupon\useCase\send\SendCouponsService;
use src\repositories\cases\CasesRepository;
use src\services\email\EmailMainService;
use src\services\email\EmailService;
use Yii;
use yii\db\StaleObjectException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class CouponController
 *
 * @property RequestCouponService $requestCoupon
 * @property SendCouponsService $sendCouponsService
 * @property CasesRepository $casesRepository
 * @property EmailMainService $emailService
 */
class CouponController extends FController
{
    private $requestCoupon;
    private $sendCouponsService;
    private $casesRepository;
    private EmailMainService $emailService;

    public function __construct(
        $id,
        $module,
        RequestCouponService $requestCoupon,
        SendCouponsService $sendCouponsService,
        CasesRepository $casesRepository,
        EmailMainService $emailService,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->requestCoupon = $requestCoupon;
        $this->sendCouponsService = $sendCouponsService;
        $this->casesRepository = $casesRepository;
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
                        $result['error'] = @Json::decode($result['error']);
                        $form->addError('general', 'Communication service error: ' . ($result['error']['name'] ?? '') . ' ( ' . ($result['error']['message']  ?? '') . ' )');
                    } else {
                        $previewEmailForm = new CaseCouponPreviewEmailForm($result['data']);
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
        $previewEmailForm = new CaseCouponPreviewEmailForm();

        if ($previewEmailForm->load(Yii::$app->request->post())) {
            if ($previewEmailForm->validate()) {
                try {
                    $case = $this->casesRepository->find($previewEmailForm->e_case_id);
                    $coupons = CouponCase::find()->getByCaseId($case ? $case->cs_id : 0)->all();

                    $mail = $this->emailService->createFromCase($previewEmailForm, $case);
                    $previewEmailForm->is_send = true;
                    $this->emailService->sendMail($mail, $attachments);
                    Yii::$app->session->setFlash('send-success', 'Success: <strong>Email Message</strong> is sent to <strong>' . $mail->getEmailTo() . '</strong>');

                    $selectedCoupons = json_decode($previewEmailForm->coupon_list, true);

                    foreach ($selectedCoupons as $couponId) {
                        $coupon = Coupon::findOne((int)$couponId);
                        if ($coupon) {
                            $coupon->c_status_id = CouponStatus::SENT;
                            $coupon->save();

                            if ($clientId = ArrayHelper::getValue($case, 'client.id')) {
                                $couponClient = CouponClient::create($coupon->c_id, $clientId);
                                (new CouponClientRepository())->save($couponClient);

                                $couponSend = CouponSend::create($coupon->c_id, Auth::id(), $mail->e_email_to);
                                (new CouponSendRepository())->save($couponSend);
                            }
                        }
                    }

                    $form = new SendCouponsForm();
                    $form->caseId = $case->cs_id;
                    return $this->render('/cases/coupons/view', [
                        'model' => $case,
                        'coupons' => $coupons,
                        'sendCouponsForm' => $form
                    ]);
                } catch (CreateModelException $e) {
                    $errorsMessage = VarDumper::dumpAsString($e->getErrors());
                    Yii::error($e->getMessage(), 'CouponController::actionSend::CreateModelException');
                    $previewEmailForm->addError('error', 'Error: Email has not been created. ' .  $errorsMessage);
                } catch (EmailNotSentException $e) {
                    $previewEmailForm->addError('error', 'Error: Email Message has not been sent to ' .  $e->getEmailTo());
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
