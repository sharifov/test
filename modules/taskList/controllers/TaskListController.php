<?php

namespace modules\taskList\controllers;

use frontend\controllers\FController;
use modules\taskList\src\entities\taskList\TaskList;
use modules\taskList\src\entities\taskList\search\TaskListSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TaskListController implements the CRUD actions for TaskList model.
 */
class TaskListController extends FController
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete-ajax' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * Lists all TaskList models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new TaskListSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TaskList model.
     * @param int $tl_id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($tl_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($tl_id),
        ]);
    }

    /**
     * Creates a new TaskList model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new TaskList();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'tl_id' => $model->tl_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TaskList model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $tl_id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($tl_id)
    {
        $model = $this->findModel($tl_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'tl_id' => $model->tl_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TaskList model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $tl_id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($tl_id)
    {
        $this->findModel($tl_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TaskList model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $tl_id ID
     * @return TaskList the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($tl_id)
    {
        if (($model = TaskList::findOne(['tl_id' => $tl_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
