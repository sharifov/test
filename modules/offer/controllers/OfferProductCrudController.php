<?php

namespace modules\offer\controllers;

use sales\auth\Auth;
use Yii;
use modules\offer\src\entities\offerProduct\OfferProduct;
use modules\offer\src\entities\offerProduct\search\OfferProductCrudSearch;
use frontend\controllers\FController;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * Class OfferProductCrudController
 */
class OfferProductCrudController extends FController
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
        $searchModel = new OfferProductCrudSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $op_offer_id
     * @param $op_product_quote_id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($op_offer_id, $op_product_quote_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($op_offer_id, $op_product_quote_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new OfferProduct();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'op_offer_id' => $model->op_offer_id, 'op_product_quote_id' => $model->op_product_quote_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param $op_offer_id
     * @param $op_product_quote_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($op_offer_id, $op_product_quote_id)
    {
        $model = $this->findModel($op_offer_id, $op_product_quote_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'op_offer_id' => $model->op_offer_id, 'op_product_quote_id' => $model->op_product_quote_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $op_offer_id
     * @param $op_product_quote_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($op_offer_id, $op_product_quote_id): Response
    {
        $this->findModel($op_offer_id, $op_product_quote_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param $op_offer_id
     * @param $op_product_quote_id
     * @return OfferProduct
     * @throws NotFoundHttpException
     */
    protected function findModel($op_offer_id, $op_product_quote_id): OfferProduct
    {
        if (($model = OfferProduct::findOne(['op_offer_id' => $op_offer_id, 'op_product_quote_id' => $op_product_quote_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
