<?php

namespace frontend\controllers;

use sales\helpers\app\AppHelper;
use sales\model\coupon\useCase\request\RequestCouponService;
use sales\model\coupon\useCase\request\RequestForm;
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
 */
class CouponController extends FController
{
    private $requestCoupon;

    public function __construct($id, $module, RequestCouponService $requestCoupon, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->requestCoupon = $requestCoupon;
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
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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

        $form = new RequestForm($caseId);

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
                    Yii::error($e, 'CouponController:' . __FUNCTION__ );
                    return $this->asJson(['success' => false, 'message' => 'Server error']);
                }
            }
            return $this->asJson(\common\components\bootstrap4\activeForm\ActiveForm::formatError($form));
        }

        return $this->renderAjax('_request_form', [
            'model' => $form,
        ]);
    }
}
