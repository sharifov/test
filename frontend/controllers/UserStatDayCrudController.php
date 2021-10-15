<?php

namespace frontend\controllers;

use sales\model\userStatDay\entity\UserStatDay;
use sales\model\userStatDay\entity\search\UserStatDaySearch;
use frontend\controllers\FController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserStatDayCrudController implements the CRUD actions for UserStatDay model.
 */
class UserStatDayCrudController extends FController
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
     * Lists all UserStatDay models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserStatDaySearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserStatDay model.
     * @param int $usd_id ID
     * @param int $usd_month Month
     * @param int $usd_year Year
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($usd_id, $usd_month, $usd_year)
    {
        return $this->render('view', [
            'model' => $this->findModel($usd_id, $usd_month, $usd_year),
        ]);
    }

    /**
     * Creates a new UserStatDay model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserStatDay();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'usd_id' => $model->usd_id, 'usd_month' => $model->usd_month, 'usd_year' => $model->usd_year]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UserStatDay model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $usd_id ID
     * @param int $usd_month Month
     * @param int $usd_year Year
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($usd_id, $usd_month, $usd_year)
    {
        $model = $this->findModel($usd_id, $usd_month, $usd_year);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'usd_id' => $model->usd_id, 'usd_month' => $model->usd_month, 'usd_year' => $model->usd_year]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UserStatDay model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $usd_id ID
     * @param int $usd_month Month
     * @param int $usd_year Year
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($usd_id, $usd_month, $usd_year)
    {
        $this->findModel($usd_id, $usd_month, $usd_year)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UserStatDay model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $usd_id ID
     * @param int $usd_month Month
     * @param int $usd_year Year
     * @return UserStatDay the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($usd_id, $usd_month, $usd_year)
    {
        if (($model = UserStatDay::findOne(['usd_id' => $usd_id, 'usd_month' => $usd_month, 'usd_year' => $usd_year])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
