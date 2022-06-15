<?php

namespace modules\taskList\controllers;

use frontend\controllers\FController;
use modules\taskList\src\entities\userTask\repository\UserTaskRepository;
use modules\taskList\src\entities\userTask\UserTask;
use modules\taskList\src\entities\userTask\UserTaskSearch;
use src\helpers\app\AppHelper;
use src\helpers\ErrorsToStringHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserTaskCrudController implements the CRUD actions for UserTask model.
 */
class UserTaskCrudController extends FController
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
            ]
        );
    }

    /**
     * Lists all UserTask models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserTaskSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserTask model.
     * @param int $ut_id ID
     * @param int $ut_year Year
     * @param int $ut_month Month
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($ut_id, $ut_year, $ut_month)
    {
        return $this->render('view', [
            'model' => $this->findModel($ut_id, $ut_year, $ut_month),
        ]);
    }

    /**
     * Creates a new UserTask model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new UserTask();

        if ($this->request->isPost) {
            try {
                if (!$model->load($this->request->post())) {
                    throw new \RuntimeException('UserTask not loaded');
                }
                if (!$model->validate()) {
                    throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($model, ' '));
                }
                (new UserTaskRepository($model))->save();
                return $this->redirect(['view', 'ut_id' => $model->ut_id, 'ut_year' => $model->ut_year, 'ut_month' => $model->ut_month]);
            } catch (\RuntimeException | \DomainException $throwable) {
                \Yii::error(AppHelper::throwableLog($throwable), 'UserTaskCrudController:actionCreate:Exception');
                \Yii::$app->getSession()->setFlash('warning', $throwable->getMessage());
            } catch (\Throwable $throwable) {
                \Yii::error(AppHelper::throwableLog($throwable), 'UserTaskCrudController:actionCreate:Throwable');
                \Yii::$app->getSession()->setFlash('error', $throwable->getMessage());
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UserTask model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $ut_id ID
     * @param int $ut_year Year
     * @param int $ut_month Month
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
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
     * Deletes an existing UserTask model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $ut_id ID
     * @param int $ut_year Year
     * @param int $ut_month Month
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($ut_id, $ut_year, $ut_month)
    {
        $this->findModel($ut_id, $ut_year, $ut_month)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UserTask model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $ut_id ID
     * @param int $ut_year Year
     * @param int $ut_month Month
     * @return UserTask the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($ut_id, $ut_year, $ut_month)
    {
        if (($model = UserTask::findOne(['ut_id' => $ut_id, 'ut_year' => $ut_year, 'ut_month' => $ut_month])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
