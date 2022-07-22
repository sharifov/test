<?php

namespace modules\taskList\controllers;

use modules\taskList\src\entities\userTask\UserTaskStatusLog;
use modules\taskList\src\entities\userTask\UserTaskStatusLogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserTaskStatusLogCrudController implements the CRUD actions for UserTaskStatusLog model.
 */
class UserTaskStatusLogCrudController extends Controller
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
     * Lists all UserTaskStatusLog models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserTaskStatusLogSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserTaskStatusLog model.
     * @param int $utsl_id Utsl ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($utsl_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($utsl_id),
        ]);
    }

    /**
     * Creates a new UserTaskStatusLog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new UserTaskStatusLog();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'utsl_id' => $model->utsl_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UserTaskStatusLog model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $utsl_id Utsl ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($utsl_id)
    {
        $model = $this->findModel($utsl_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'utsl_id' => $model->utsl_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UserTaskStatusLog model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $utsl_id Utsl ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($utsl_id)
    {
        $this->findModel($utsl_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UserTaskStatusLog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $utsl_id Utsl ID
     * @return UserTaskStatusLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($utsl_id)
    {
        if (($model = UserTaskStatusLog::findOne(['utsl_id' => $utsl_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
