<?php

namespace frontend\controllers;

use Yii;
use src\model\coupon\entity\couponProduct\CouponProduct;
use src\model\coupon\entity\couponProduct\CouponProductSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

/**
 * Class CouponProductCrudController
 */
class CouponProductCrudController extends FController
{
    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

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
        $searchModel = new CouponProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $cup_coupon_id
     * @param integer $cup_product_type_id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($cup_coupon_id, $cup_product_type_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($cup_coupon_id, $cup_product_type_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new CouponProduct();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'cup_coupon_id' => $model->cup_coupon_id, 'cup_product_type_id' => $model->cup_product_type_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $cup_coupon_id
     * @param integer $cup_product_type_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($cup_coupon_id, $cup_product_type_id)
    {
        $model = $this->findModel($cup_coupon_id, $cup_product_type_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'cup_coupon_id' => $model->cup_coupon_id, 'cup_product_type_id' => $model->cup_product_type_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $cup_coupon_id
     * @param integer $cup_product_type_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($cup_coupon_id, $cup_product_type_id): Response
    {
        $this->findModel($cup_coupon_id, $cup_product_type_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $cup_coupon_id
     * @param integer $cup_product_type_id
     * @return CouponProduct
     * @throws NotFoundHttpException
     */
    protected function findModel($cup_coupon_id, $cup_product_type_id): CouponProduct
    {
        if (($model = CouponProduct::findOne(['cup_coupon_id' => $cup_coupon_id, 'cup_product_type_id' => $cup_product_type_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('CouponProduct does not exist.');
    }
}
