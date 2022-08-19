<?php

namespace modules\objectTask\controllers;

use frontend\controllers\FController;
use modules\objectTask\src\entities\ObjectTaskScenario;
use modules\objectTask\src\entities\ObjectTaskScenarioSearch;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ObjectTaskScenarioController implements the CRUD actions for ObjectTaskScenario model.
 */
class ObjectTaskScenarioController extends FController
{
    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all ObjectTaskScenario models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ObjectTaskScenarioSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ObjectTaskScenario model.
     * @param int $ots_id Ots ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($ots_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($ots_id),
        ]);
    }

    /**
     * Creates a new ObjectTaskScenario model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate(?string $key = null)
    {
        $model = new ObjectTaskScenario();

        if ($key !== null) {
            $model->ots_key = $key;
            $model->ots_data_json = $model->getScenarioTemplate();
        }

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'ots_id' => $model->ots_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ObjectTaskScenario model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $ots_id Ots ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($ots_id, ?string $key = null)
    {
        $model = $this->findModel($ots_id);

        if ($key !== null) {
            $model->ots_key = $key;
            $model->ots_data_json = $model->getScenarioTemplate();
        }

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ots_id' => $model->ots_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ObjectTaskScenario model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $ots_id Ots ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($ots_id)
    {
        $this->findModel($ots_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ObjectTaskScenario model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $ots_id Ots ID
     * @return ObjectTaskScenario the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($ots_id)
    {
        if (($model = ObjectTaskScenario::findOne(['ots_id' => $ots_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
