<?php

namespace frontend\controllers;

use common\models\DateSensitive;
use common\models\search\DateSensitiveSearch;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * DateSensitiveCrudController implements the CRUD actions for DateSensitive model.
 */
class DateSensitiveCrudController extends FController
{
    /**
     * @inheritDoc
     */
    public function behaviors(): array
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
     * Lists all DateSensitive models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new DateSensitiveSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DateSensitive model.
     * @param $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new DateSensitive model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new DateSensitive();

        if ($this->request->isPost && $model->load($this->request->post())) {
//            $model->generateKey();
//            if ($model->save()) {
//                return $this->redirect(['view', 'qs_id' => $model->qs_id]);
//            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing DateSensitive model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $qs_id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post())) {
//            $model->generateKey();
//            if ($model->save()) {
//                return $this->redirect(['view', 'qs_id' => $model->qs_id]);
//            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing DateSensitive model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the DateSensitive model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param $id
     * @return DateSensitive the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DateSensitive::findOne(['da_id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Date Sensitive not found by ID(' . $id . ')');
    }
}
