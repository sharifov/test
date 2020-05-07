<?php

namespace frontend\controllers;

use Yii;
use sales\model\coupon\entity\couponCase\CouponCase;
use sales\model\coupon\entity\couponCase\search\CouponCaseSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

class CouponCaseController extends FController
{
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
        $searchModel = new CouponCaseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $cc_coupon_id
     * @param integer $cc_case_id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($cc_coupon_id, $cc_case_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($cc_coupon_id, $cc_case_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new CouponCase();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'cc_coupon_id' => $model->cc_coupon_id, 'cc_case_id' => $model->cc_case_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $cc_coupon_id
     * @param integer $cc_case_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($cc_coupon_id, $cc_case_id)
    {
        $model = $this->findModel($cc_coupon_id, $cc_case_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'cc_coupon_id' => $model->cc_coupon_id, 'cc_case_id' => $model->cc_case_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $cc_coupon_id
     * @param integer $cc_case_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($cc_coupon_id, $cc_case_id): Response
    {
        $this->findModel($cc_coupon_id, $cc_case_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $cc_coupon_id
     * @param integer $cc_case_id
     * @return CouponCase
     * @throws NotFoundHttpException
     */
    protected function findModel($cc_coupon_id, $cc_case_id): CouponCase
    {
        if (($model = CouponCase::findOne(['cc_coupon_id' => $cc_coupon_id, 'cc_case_id' => $cc_case_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
