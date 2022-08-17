<?php

namespace modules\objectTask\controllers;

use modules\objectTask\src\entities\ObjectTask;
use modules\objectTask\src\entities\ObjectTaskSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ObjectTaskCrudController implements the CRUD actions for ObjectTask model.
 */
class ObjectTaskCrudController extends Controller
{
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
     * Lists all ObjectTask models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ObjectTaskSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ObjectTask model.
     * @param string $ot_uuid Ot Uuid
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($ot_uuid)
    {
        return $this->render('view', [
            'model' => $this->findModel($ot_uuid),
        ]);
    }

    /**
     * Creates a new ObjectTask model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new ObjectTask();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'ot_uuid' => $model->ot_uuid]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ObjectTask model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $ot_uuid Ot Uuid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($ot_uuid)
    {
        $model = $this->findModel($ot_uuid);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ot_uuid' => $model->ot_uuid]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ObjectTask model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $ot_uuid Ot Uuid
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($ot_uuid)
    {
        $this->findModel($ot_uuid)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ObjectTask model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $ot_uuid Ot Uuid
     * @return ObjectTask the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($ot_uuid)
    {
        if (($model = ObjectTask::findOne(['ot_uuid' => $ot_uuid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
