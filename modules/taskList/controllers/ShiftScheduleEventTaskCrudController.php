<?php

namespace modules\taskList\controllers;

use Yii;
use modules\taskList\src\entities\shiftScheduleEventTask\ShiftScheduleEventTask;
use modules\taskList\src\entities\shiftScheduleEventTask\ShiftScheduleEventTaskSearch;
use frontend\controllers\FController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

class ShiftScheduleEventTaskCrudController extends FController
{
    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    public function behaviors(): array
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
            ]
        );
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new ShiftScheduleEventTaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $sset_event_id Sset Event ID
     * @param int $sset_user_task_id Sset User Task ID
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($sset_event_id, $sset_user_task_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($sset_event_id, $sset_user_task_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new ShiftScheduleEventTask();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'sset_event_id' => $model->sset_event_id, 'sset_user_task_id' => $model->sset_user_task_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $sset_event_id Sset Event ID
     * @param int $sset_user_task_id Sset User Task ID
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($sset_event_id, $sset_user_task_id)
    {
        $model = $this->findModel($sset_event_id, $sset_user_task_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'sset_event_id' => $model->sset_event_id, 'sset_user_task_id' => $model->sset_user_task_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $sset_event_id Sset Event ID
     * @param int $sset_user_task_id Sset User Task ID
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($sset_event_id, $sset_user_task_id): Response
    {
        $this->findModel($sset_event_id, $sset_user_task_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param int $sset_event_id Sset Event ID
     * @param int $sset_user_task_id Sset User Task ID
     * @return ShiftScheduleEventTask
     * @throws NotFoundHttpException
     */
    protected function findModel($sset_event_id, $sset_user_task_id): ShiftScheduleEventTask
    {
        if (($model = ShiftScheduleEventTask::findOne(['sset_event_id' => $sset_event_id, 'sset_user_task_id' => $sset_user_task_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
