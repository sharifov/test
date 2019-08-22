<?php

namespace frontend\controllers;

use Yii;
use common\models\CaseSale;
use common\models\search\CaseSaleSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CaseSaleController implements the CRUD actions for CaseSale model.
 */
class CaseSaleController extends FController
{

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
     * Lists all CaseSale models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CaseSaleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CaseSale model.
     * @param integer $css_cs_id
     * @param integer $css_sale_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($css_cs_id, $css_sale_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($css_cs_id, $css_sale_id),
        ]);
    }

    /**
     * Creates a new CaseSale model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CaseSale();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'css_cs_id' => $model->css_cs_id, 'css_sale_id' => $model->css_sale_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing CaseSale model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $css_cs_id
     * @param integer $css_sale_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($css_cs_id, $css_sale_id)
    {
        $model = $this->findModel($css_cs_id, $css_sale_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'css_cs_id' => $model->css_cs_id, 'css_sale_id' => $model->css_sale_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing CaseSale model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $css_cs_id
     * @param integer $css_sale_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($css_cs_id, $css_sale_id)
    {
        $this->findModel($css_cs_id, $css_sale_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the CaseSale model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $css_cs_id
     * @param integer $css_sale_id
     * @return CaseSale the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($css_cs_id, $css_sale_id)
    {
        if (($model = CaseSale::findOne(['css_cs_id' => $css_cs_id, 'css_sale_id' => $css_sale_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
