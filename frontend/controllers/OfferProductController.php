<?php

namespace frontend\controllers;

use Yii;
use common\models\OfferProduct;
use common\models\search\OfferProductSearch;
use frontend\controllers\FController;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OfferProductController implements the CRUD actions for OfferProduct model.
 */
class OfferProductController extends FController
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
     * Lists all OfferProduct models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OfferProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OfferProduct model.
     * @param integer $op_offer_id
     * @param integer $op_product_quote_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($op_offer_id, $op_product_quote_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($op_offer_id, $op_product_quote_id),
        ]);
    }

    /**
     * Creates a new OfferProduct model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
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
     * Updates an existing OfferProduct model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $op_offer_id
     * @param integer $op_product_quote_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
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
     * Deletes an existing OfferProduct model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $op_offer_id
     * @param integer $op_product_quote_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($op_offer_id, $op_product_quote_id)
    {
        $this->findModel($op_offer_id, $op_product_quote_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the OfferProduct model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $op_offer_id
     * @param integer $op_product_quote_id
     * @return OfferProduct the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($op_offer_id, $op_product_quote_id)
    {
        if (($model = OfferProduct::findOne(['op_offer_id' => $op_offer_id, 'op_product_quote_id' => $op_product_quote_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
