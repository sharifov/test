<?php

namespace frontend\controllers;

use modules\user\userFeedback\entity\UserFeedbackFile;
use modules\user\userFeedback\entity\search\UserFeedbackFileSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserFeedbackFileCrudController implements the CRUD actions for UserFeedbackFile model.
 */
class UserFeedbackFileCrudController extends FController
{
    /**
     * @inheritDoc
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

    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
        $this->setViewPath('@frontend/views/user/user-feedback-file-crud');
    }

    /**
     * Lists all UserFeedbackFile models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserFeedbackFileSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserFeedbackFile model.
     * @param int $uff_id File ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($uff_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($uff_id),
        ]);
    }

    /**
     * Creates a new UserFeedbackFile model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new UserFeedbackFile();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'uff_id' => $model->uff_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UserFeedbackFile model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $uff_id File ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($uff_id)
    {
        $model = $this->findModel($uff_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'uff_id' => $model->uff_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UserFeedbackFile model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $uff_id File ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($uff_id)
    {
        $this->findModel($uff_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UserFeedbackFile model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $uff_id File ID
     * @return UserFeedbackFile the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($uff_id)
    {
        if (($model = UserFeedbackFile::findOne(['uff_id' => $uff_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
