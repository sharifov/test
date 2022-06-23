<?php

namespace frontend\controllers;

use common\models\SaleCreditCard;
use frontend\helpers\JsonHelper;
use frontend\models\form\CreditCardForm;
use modules\cases\src\abac\saleList\SaleListAbacDto;
use modules\cases\src\abac\saleList\SaleListAbacObject;
use src\forms\caseSale\CaseSaleSendCcInfoForm;
use src\helpers\app\AppHelper;
use src\helpers\cases\CaseSaleHelper;
use src\repositories\cases\CasesRepository;
use src\repositories\cases\CasesSaleRepository;
use src\repositories\client\ClientRepository;
use src\repositories\NotFoundException;
use src\services\cases\CasesSaleService;
use Yii;
use common\models\CreditCard;
use common\models\search\CreditCardSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CreditCardController implements the CRUD actions for CreditCard model.
 *
 * @property CasesSaleService $casesSaleService
 * @property CasesSaleRepository $casesSaleRepository
 * @property CasesRepository $casesRepository
 * @property ClientRepository $clientRepository
 */
class CreditCardController extends FController
{
    /**
     * @var CasesSaleService
     */
    private $casesSaleService;

    /**
     * @var CasesSaleRepository
     */
    private $casesSaleRepository;
    /**
     * @var CasesRepository
     */
    private CasesRepository $casesRepository;
    /**
     * @var ClientRepository
     */
    private ClientRepository $clientRepository;

    public function __construct($id, $module, CasesSaleService $casesSaleService, CasesSaleRepository $casesSaleRepository, CasesRepository $casesRepository, ClientRepository $clientRepository, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->casesSaleService = $casesSaleService;
        $this->casesSaleRepository = $casesSaleRepository;
        $this->casesRepository = $casesRepository;
        $this->clientRepository = $clientRepository;
    }

    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
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
     * Lists all CreditCard models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CreditCardSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CreditCard model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CreditCard model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $modelForm = new CreditCardForm();
        $modelCc = new CreditCard();

        if ($modelForm->load(Yii::$app->request->post()) && $modelForm->validate()) {
            $modelCc->attributes = $modelForm->attributes;
            $modelCc->updateSecureCardNumber();
            $modelCc->updateSecureCvv();

            if ($modelCc->save()) {
                return $this->redirect(['view', 'id' => $modelCc->cc_id]);
            }
        }

        return $this->render('create', [
            'model' => $modelForm,
            'modelCc' => $modelCc,
        ]);
    }

    /**
     * Updates an existing CreditCard model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $modelCc = $this->findModel($id);

        $modelForm = new CreditCardForm();
        if ($modelForm->load(Yii::$app->request->post())) {
            if ($modelForm->validate()) {
                $modelCc->attributes = $modelForm->attributes;

                $modelCc->cc_expiration_month = $modelForm->cc_expiration_month;
                $modelCc->cc_expiration_year = $modelForm->cc_expiration_year;

                $modelCc->updateSecureCardNumber();
                if (!empty($modelCc->cc_cvv)) {
                    $modelCc->updateSecureCvv();
                } else {
                    unset($modelCc->cc_cvv);
                }

                if ($modelCc->save()) {
                    return $this->redirect(['view', 'id' => $modelCc->cc_id]);
                } else {
                    Yii::error(VarDumper::dumpAsString($modelCc->errors), 'CreditCard:Update:save');
                }
            }
        } else {
            $modelForm->attributes = $modelCc->attributes;
            $modelForm->cc_number = str_replace('*', '0', ($modelCc->cc_display_number ?? '')); // $modelCc->initNumber;
            $modelForm->cc_cvv = ''; // $modelCc->initCvv;
            $modelForm->cc_expiration = date('m / y', strtotime($modelCc->cc_expiration_year . '-' . $modelCc->cc_expiration_month . '-01'));
        }

        $modelForm->cc_id = $modelCc->cc_id;

        return $this->render('update', [
            'model' => $modelForm,
            'modelCc' => $modelCc,
        ]);
    }

    public function actionAjaxUpdate()
    {
        $id = Yii::$app->request->get('id');
        $pjaxId = Yii::$app->request->get('pjaxId');

        if (!Yii::$app->request->isPost && !Yii::$app->request->isAjax) {
            throw new BadRequestHttpException('actionAjaxUpdate is AJAX only action');
        }

        $modelCc = $this->findModel($id);

        if (!$modelCc) {
            throw new NotFoundException('Credit Card data is not found');
        }

        try {
            $modelCc->scenario = CreditCard::SCENARIO_CASE_AJAX_UPDATE;

            if ($modelCc->load(Yii::$app->request->post()) && $modelCc->validate()) {
                if ($modelCc->save()) {
                    return '<script>$("#modal-sm").modal("hide"); pjaxReload({container: "#' . $pjaxId . '"}); createNotify("Success Updated", "Credit Card Successfully updated", "success")</script>';
                }
                throw new \RuntimeException($modelCc->getErrorSummary(false)[0]);
            }
        } catch (\Throwable $e) {
            $modelCc->addError('general', $e->getMessage());
        }

        return $this->renderAjax('_form_ajax_update', [
            'model' => $modelCc,
        ]);
    }

    public function actionAjaxAddCreditCard()
    {

        $caseId = Yii::$app->request->get('caseId');
        $saleId = Yii::$app->request->get('saleId');
        $pjaxId = Yii::$app->request->get('pjaxId');

        if (!$caseId || !$saleId) {
            throw new BadRequestHttpException();
        }

        try {
            $case = $this->casesRepository->find((int)$caseId);
        } catch (NotFoundException $e) {
            throw new NotFoundHttpException('Not found case');
        }

        $userId = \Yii::$app->user->id;
        $caseAbacDto = new SaleListAbacDto($case, $userId);
        /** @abac $caseAbacDto, CasesAbacObject::UI_BLOCK_SALE_LIST, CasesAbacObject::ACTION_ADD_CREDIT_CARD, Restrict access to add credit card */
        if (!Yii::$app->abac->can($caseAbacDto, SaleListAbacObject::UI_BLOCK_SALE_LIST, SaleListAbacObject::ACTION_ADD_CREDIT_CARD)) {
            throw new ForbiddenHttpException('Access denied');
        }

        try {
            $form = new CreditCardForm();

            if ($form->load(Yii::$app->request->post()) && $form->validate()) {
                $caseSale = $this->casesSaleRepository->getSaleByPrimaryKeys((int)$caseId, (int)$saleId);

                $model = new CreditCard();
                $model->attributes = $form->attributes;
                $model->cc_status_id = CreditCard::STATUS_VALID;
                $model->updateSecureCardNumber();
                $model->updateSecureCvv();

                if ($model->save()) {
                    $saleCreditCard = new SaleCreditCard();
                    $saleCreditCard->scc_sale_id = $saleId;
                    $saleCreditCard->scc_cc_id = $model->primaryKey;

                    if (!$saleCreditCard->save()) {
                        throw new \RuntimeException($saleCreditCard->getErrorSummary(false)[0]);
                    } else {
                        $apiKey = $this->casesSaleRepository->getProjectApiKey($caseSale);

                        if (!$bookId = $caseSale->css_sale_book_id) {
                            $bookId = $caseSale->css_sale_data['bookingId'] ?? '';
                        }

                        $result = $this->casesSaleService->sendAddedCreditCardToBO($apiKey, $bookId, $caseSale->css_sale_id, $form);

                        if ($result['error']) {
                            $message = Html::encode(str_replace(['"', "'"], '', $result['message']));
                            $notify = 'createNotify("B/O add card notice", "' . $message . '", "warning")';
                            return '<script>$("#modal-df").modal("hide"); pjaxReload({container: "#' . $pjaxId . '"}); ' . $notify . '</script>';
                        }

                        $model->cc_is_sync_bo = 1;
                        $model->save();

                        return '<script>$("#modal-df").modal("hide"); pjaxReload({container: "#' . $pjaxId . '"}); createNotify("Success", "Credit Card Successfully created", "success");</script>';
                    }
                }
            }
        } catch (\Throwable $e) {
            $form->addError('general', $e->getMessage());
        }

        return $this->renderAjax('_form_ajax_create', [
            'model' => $form,
        ]);
    }

    public function actionAjaxSendCcInfo()
    {
        $caseId = Yii::$app->request->get('caseId', null);
        $saleId = Yii::$app->request->get('saleId', null);

        $form = new CaseSaleSendCcInfoForm();
        $caseSale = null;

        try {
            $case = $this->casesRepository->find((int)$caseId);
        } catch (NotFoundException $e) {
            throw new NotFoundHttpException('Not found case');
        }

        $userId = \Yii::$app->user->id;
        $caseAbacDto = new SaleListAbacDto($case, $userId);
        /** @abac $caseAbacDto, CasesAbacObject::UI_BLOCK_SALE_LIST, CasesAbacObject::ACTION_SEND_CC_INFO, Restrict access to send credit card info */
        if (!Yii::$app->abac->can($caseAbacDto, SaleListAbacObject::UI_BLOCK_SALE_LIST, SaleListAbacObject::ACTION_SEND_CC_INFO)) {
            throw new ForbiddenHttpException('Access denied');
        }

        try {
            $caseSale = $this->casesSaleRepository->getSaleByPrimaryKeys((int)$caseId, (int)$saleId);
            $client = $this->clientRepository->find($case->cs_client_id);
            $customerEmail = CaseSaleHelper::getCustomerEmail(JsonHelper::decode($caseSale->css_sale_data));

            if ($customerEmail) {
                $form->emailList[$customerEmail] = $customerEmail;
            }
            $clientEmailList = ArrayHelper::merge($form->emailList, $client->emailList);
            $form->emailList = $clientEmailList;

            if (Yii::$app->request->isPjax && $form->load(Yii::$app->request->post()) && $form->validate()) {
                $apiKey = $this->casesSaleRepository->getProjectApiKey($caseSale);
                $dataSale = JsonHelper::decode($caseSale->css_sale_data_updated);
                $result = $this->casesSaleService->sendCcInfo($apiKey, $caseSale->css_sale_id, (string)($dataSale['bookingId'] ?? ''), $form->email);
                if ($result['error']) {
                    throw new \RuntimeException('B/O error has occurred: ' . $result['message']);
                }
                return '<script>$("#modal-sm").modal("hide"); createNotify("Success", "Email sent to customer successfully", "success")</script>';
            }
        } catch (NotFoundException | \RuntimeException $e) {
            $form->addError('general', $e->getMessage());
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableFormatter($e), 'CreditCardController::actionAjaxSendCcInfo::Throwable');
            $form->addError('general', 'Internal Server Error');
        }

        return $this->renderAjax('partial/_send_cc_info_form', [
            'formCaseSale' => $form,
        ]);
    }

    public function actionAjaxDelete()
    {
        $id = Yii::$app->request->get('id');
        $saleId = Yii::$app->request->get('saleId');

        try {
            $model = SaleCreditCard::findOne(['scc_cc_id' => $id, 'scc_sale_id' => $saleId]);

            if (!$model) {
                throw new NotFoundException('Credit Card data is not found');
            }

            if (!$model->delete()) {
                throw new \RuntimeException($model->getErrorSummary(false)[0]);
            }

            return $this->asJson(['error' => false, 'message' => 'Credit Card Successfully deleted']);
        } catch (\Throwable $e) {
            $message = $e->getMessage();
        }
        return $this->asJson(['error' => true, 'message' => $message]);
    }

    /**
     * Deletes an existing CreditCard model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the CreditCard model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CreditCard the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CreditCard::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
