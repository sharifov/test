<?php

namespace modules\objectTask\controllers;

use frontend\controllers\FController;
use modules\objectTask\src\abac\ObjectTaskObject;
use modules\objectTask\src\entities\ObjectTaskStatusLog;
use modules\objectTask\src\entities\ObjectTaskStatusLogSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ObjectTaskStatusLogController implements the CRUD actions for ObjectTaskStatusLog model.
 */
class ObjectTaskStatusLogController extends FController
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
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        /** @abac ObjectTaskObject::ACT_OBJECT_TASK_STATUS_LOG, ObjectTaskObject::ACTION_ACCESS, Access to page object-task/object-task-status-log/(index|view) */
                        [
                            'actions' => ['index', 'view'],
                            'allow' => \Yii::$app->abac->can(
                                null,
                                ObjectTaskObject::ACT_OBJECT_TASK_STATUS_LOG,
                                ObjectTaskObject::ACTION_ACCESS
                            ),
                            'roles' => ['@'],
                        ],
                        /** @abac ObjectTaskObject::ACT_OBJECT_TASK_STATUS_LOG, ObjectTaskObject::ACTION_UPDATE, Access to page object-task/object-task-status-log/update */
                        [
                            'actions' => ['update'],
                            'allow' => \Yii::$app->abac->can(
                                null,
                                ObjectTaskObject::ACT_OBJECT_TASK_STATUS_LOG,
                                ObjectTaskObject::ACTION_UPDATE
                            ),
                            'roles' => ['@'],
                        ],
                        /** @abac ObjectTaskObject::ACT_OBJECT_TASK_STATUS_LOG, ObjectTaskObject::ACTION_UPDATE, Access to page object-task/object-task-status-log/delete */
                        [
                            'actions' => ['delete'],
                            'allow' => \Yii::$app->abac->can(
                                null,
                                ObjectTaskObject::ACT_OBJECT_TASK_STATUS_LOG,
                                ObjectTaskObject::ACTION_DELETE
                            ),
                            'roles' => ['@'],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all ObjectTaskStatusLog models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ObjectTaskStatusLogSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ObjectTaskStatusLog model.
     * @param int $otsl_id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($otsl_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($otsl_id),
        ]);
    }

    /**
     * Creates a new ObjectTaskStatusLog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new ObjectTaskStatusLog();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'otsl_id' => $model->otsl_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ObjectTaskStatusLog model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $otsl_id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($otsl_id)
    {
        $model = $this->findModel($otsl_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'otsl_id' => $model->otsl_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ObjectTaskStatusLog model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $otsl_id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($otsl_id)
    {
        $this->findModel($otsl_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ObjectTaskStatusLog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $otsl_id ID
     * @return ObjectTaskStatusLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($otsl_id)
    {
        if (($model = ObjectTaskStatusLog::findOne(['otsl_id' => $otsl_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
