<?php

namespace frontend\controllers;

use Yii;
use sales\model\user\entity\monitor\UserMonitor;
use sales\model\user\entity\monitor\search\UserMonitorSearch;
use frontend\controllers\FController;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserMonitorController implements the CRUD actions for UserMonitor model.
 */
class UserMonitorController extends FController
{
    /**
     * @return array
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

    /**
     * Lists all UserMonitor models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserMonitorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string
     */
    public function actionStats(): string
    {
        $searchModel = new UserMonitorSearch();

        $startDateTime = date('Y-m-d H:i', strtotime('-1 day'));
        $endDateTime = date('Y-m-d H:i', strtotime('+10 hours'));

        $params = Yii::$app->request->queryParams;

        if (!empty($params) && $params['UserMonitorSearch']['startTime'] && $params['UserMonitorSearch']['endTime']) {
            $startDateTime = $params['UserMonitorSearch']['startTime'];
            $endDateTime = $params['UserMonitorSearch']['endTime'];
        }

        $data = $searchModel->searchStats($params, $startDateTime);

        return $this->render('stats', [
            'data' => $data,
            'searchModel' => $searchModel,
            'startDateTime' => $startDateTime,
            'endDateTime' => $endDateTime,
        ]);
    }

    /**
     * Displays a single UserMonitor model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new UserMonitor model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserMonitor();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->um_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UserMonitor model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->um_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UserMonitor model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UserMonitor model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserMonitor the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserMonitor::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
