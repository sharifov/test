<?php

namespace modules\taskList\controllers;

use modules\taskList\src\entities\userTask\repository\UserTaskRepository;
use src\helpers\app\AppHelper;
use Yii;
use modules\taskList\src\entities\userTask\UserTask;
use modules\taskList\src\entities\userTask\UserTaskSearch;
use frontend\controllers\FController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

class UserTaskCrudController extends FController
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
        $searchModel = new UserTaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $ut_id ID
     * @param int $ut_year Year
     * @param int $ut_month Month
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($ut_id, $ut_year, $ut_month): string
    {
        return $this->render('view', [
            'model' => $this->findModel($ut_id, $ut_year, $ut_month),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new UserTask();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->validate()) {
                try {
                    (new UserTaskRepository($model))->save();
                } catch (\Throwable $throwable) {
                    \Yii::error(AppHelper::throwableLog($throwable),'UserTaskCrudController:actionCreate:save');
                    Yii::$app->getSession()->setFlash('error', $throwable->getMessage());
                }
                return $this->redirect(['view', 'ut_id' => $model->ut_id, 'ut_year' => $model->ut_year, 'ut_month' => $model->ut_month]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $ut_id ID
     * @param int $ut_year Year
     * @param int $ut_month Month
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($ut_id, $ut_year, $ut_month)
    {
        $model = $this->findModel($ut_id, $ut_year, $ut_month);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ut_id' => $model->ut_id, 'ut_year' => $model->ut_year, 'ut_month' => $model->ut_month]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $ut_id ID
     * @param int $ut_year Year
     * @param int $ut_month Month
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($ut_id, $ut_year, $ut_month): Response
    {
        $this->findModel($ut_id, $ut_year, $ut_month)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param int $ut_id ID
     * @param int $ut_year Year
     * @param int $ut_month Month
     * @return UserTask
     * @throws NotFoundHttpException
     */
    protected function findModel($ut_id, $ut_year, $ut_month): UserTask
    {
        if (($model = UserTask::findOne(['ut_id' => $ut_id, 'ut_year' => $ut_year, 'ut_month' => $ut_month])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
