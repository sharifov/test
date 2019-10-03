<?php

namespace frontend\controllers;

use Yii;
use common\models\QcallConfig;
use common\models\search\QcallConfigSearch;
use frontend\controllers\FController;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * QcallConfigController implements the CRUD actions for QcallConfig model.
 */
class QcallConfigController extends FController
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
     * Lists all QcallConfig models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new QcallConfigSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single QcallConfig model.
     * @param integer $qc_status_id
     * @param integer $qc_call_att
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($qc_status_id, $qc_call_att)
    {
        return $this->render('view', [
            'model' => $this->findModel($qc_status_id, $qc_call_att),
        ]);
    }

    /**
     * Creates a new QcallConfig model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new QcallConfig();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'qc_status_id' => $model->qc_status_id, 'qc_call_att' => $model->qc_call_att]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing QcallConfig model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $qc_status_id
     * @param integer $qc_call_att
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($qc_status_id, $qc_call_att)
    {
        $model = $this->findModel($qc_status_id, $qc_call_att);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'qc_status_id' => $model->qc_status_id, 'qc_call_att' => $model->qc_call_att]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing QcallConfig model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $qc_status_id
     * @param integer $qc_call_att
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($qc_status_id, $qc_call_att)
    {
        $this->findModel($qc_status_id, $qc_call_att)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the QcallConfig model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $qc_status_id
     * @param integer $qc_call_att
     * @return QcallConfig the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($qc_status_id, $qc_call_att)
    {
        if (($model = QcallConfig::findOne(['qc_status_id' => $qc_status_id, 'qc_call_att' => $qc_call_att])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
