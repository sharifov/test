<?php

namespace frontend\controllers;

use Yii;
use common\models\EmailUnsubscribe;
use common\models\search\EmailUnsubscribeSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EmailUnsubscribeController implements the CRUD actions for EmailUnsubscribe model.
 */
class EmailUnsubscribeController extends FController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * Lists all EmailUnsubscribe models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EmailUnsubscribeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EmailUnsubscribe model.
     * @param string $eu_email
     * @param integer $eu_project_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($eu_email, $eu_project_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($eu_email, $eu_project_id),
        ]);
    }

    /**
     * Creates a new EmailUnsubscribe model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EmailUnsubscribe();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'eu_email' => $model->eu_email, 'eu_project_id' => $model->eu_project_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing EmailUnsubscribe model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $eu_email
     * @param integer $eu_project_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($eu_email, $eu_project_id)
    {
        $model = $this->findModel($eu_email, $eu_project_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'eu_email' => $model->eu_email, 'eu_project_id' => $model->eu_project_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing EmailUnsubscribe model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $eu_email
     * @param integer $eu_project_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($eu_email, $eu_project_id)
    {
        $this->findModel($eu_email, $eu_project_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the EmailUnsubscribe model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $eu_email
     * @param integer $eu_project_id
     * @return EmailUnsubscribe the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($eu_email, $eu_project_id)
    {
        if (($model = EmailUnsubscribe::findOne(['eu_email' => $eu_email, 'eu_project_id' => $eu_project_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
