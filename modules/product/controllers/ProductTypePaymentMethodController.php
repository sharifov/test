<?php

namespace modules\product\controllers;

use Yii;
use modules\product\src\entities\productTypePaymentMethod\ProductTypePaymentMethod;
use modules\product\src\entities\productTypePaymentMethod\search\ProductTypePaymentMethodSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProductTypePaymentMethodController implements the CRUD actions for ProductTypePaymentMethod model.
 */
class ProductTypePaymentMethodController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all ProductTypePaymentMethod models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductTypePaymentMethodSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProductTypePaymentMethod model.
     * @param integer $ptpm_produt_type_id
     * @param integer $ptpm_payment_method_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($ptpm_produt_type_id, $ptpm_payment_method_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($ptpm_produt_type_id, $ptpm_payment_method_id),
        ]);
    }

    /**
     * Creates a new ProductTypePaymentMethod model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProductTypePaymentMethod();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ptpm_produt_type_id' => $model->ptpm_produt_type_id, 'ptpm_payment_method_id' => $model->ptpm_payment_method_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ProductTypePaymentMethod model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $ptpm_produt_type_id
     * @param integer $ptpm_payment_method_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($ptpm_produt_type_id, $ptpm_payment_method_id)
    {
        $model = $this->findModel($ptpm_produt_type_id, $ptpm_payment_method_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ptpm_produt_type_id' => $model->ptpm_produt_type_id, 'ptpm_payment_method_id' => $model->ptpm_payment_method_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ProductTypePaymentMethod model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $ptpm_produt_type_id
     * @param integer $ptpm_payment_method_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($ptpm_produt_type_id, $ptpm_payment_method_id)
    {
        $this->findModel($ptpm_produt_type_id, $ptpm_payment_method_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ProductTypePaymentMethod model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $ptpm_produt_type_id
     * @param integer $ptpm_payment_method_id
     * @return ProductTypePaymentMethod the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($ptpm_produt_type_id, $ptpm_payment_method_id)
    {
        if (($model = ProductTypePaymentMethod::findOne(['ptpm_produt_type_id' => $ptpm_produt_type_id, 'ptpm_payment_method_id' => $ptpm_payment_method_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
