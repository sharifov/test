<?php

namespace frontend\controllers;

use Yii;
use common\models\SaleCreditCard;
use common\models\search\SaleCreditCardSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SaleCreditCardController implements the CRUD actions for SaleCreditCard model.
 */
class SaleCreditCardController extends FController
{
    /**
     * @return array
     */
    public function behaviors()
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
     * Lists all SaleCreditCard models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SaleCreditCardSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SaleCreditCard model.
     * @param integer $scc_sale_id
     * @param integer $scc_cc_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($scc_sale_id, $scc_cc_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($scc_sale_id, $scc_cc_id),
        ]);
    }

    /**
     * Creates a new SaleCreditCard model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SaleCreditCard();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'scc_sale_id' => $model->scc_sale_id, 'scc_cc_id' => $model->scc_cc_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SaleCreditCard model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $scc_sale_id
     * @param integer $scc_cc_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($scc_sale_id, $scc_cc_id)
    {
        $model = $this->findModel($scc_sale_id, $scc_cc_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'scc_sale_id' => $model->scc_sale_id, 'scc_cc_id' => $model->scc_cc_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SaleCreditCard model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $scc_sale_id
     * @param integer $scc_cc_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($scc_sale_id, $scc_cc_id)
    {
        $this->findModel($scc_sale_id, $scc_cc_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SaleCreditCard model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $scc_sale_id
     * @param integer $scc_cc_id
     * @return SaleCreditCard the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($scc_sale_id, $scc_cc_id)
    {
        if (($model = SaleCreditCard::findOne(['scc_sale_id' => $scc_sale_id, 'scc_cc_id' => $scc_cc_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
