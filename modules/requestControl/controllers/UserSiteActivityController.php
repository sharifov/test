<?php

namespace modules\requestControl\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use src\services\cleaner\form\DbCleanerParamsForm;
use src\services\cleaner\cleaners\UserSiteActivityCleaner;
use modules\requestControl\models\UserSiteActivity;
use modules\requestControl\models\search\UserSiteActivitySearch;
use frontend\controllers\FController;

/**
 * UserSiteActivityController implements the CRUD actions for UserSiteActivity model.
 */
class UserSiteActivityController extends FController
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
    }

    /**
     * Lists all UserSiteActivity models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSiteActivitySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $cleaner = new UserSiteActivityCleaner();
        $dbCleanerParamsForm = (new DbCleanerParamsForm())
            ->setTable($cleaner->getTable())
            ->setColumn($cleaner->getColumn());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'modelCleaner' => $dbCleanerParamsForm,
        ]);
    }


    /**
     * Lists all UserSiteActivity models.
     * @return string
     * @throws \yii\db\Exception
     */
    public function actionReport()
    {
        $searchModel = new UserSiteActivitySearch();
        $data = $searchModel->searchReport(Yii::$app->request->queryParams);

        return $this->render('report', [
            'searchModel' => $searchModel,
            'data' => $data,
        ]);
    }


    /**
     * Displays a single UserSiteActivity model.
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
     * Creates a new UserSiteActivity model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserSiteActivity();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->usa_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UserSiteActivity model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->usa_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UserSiteActivity model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UserSiteActivity model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserSiteActivity the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserSiteActivity::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @return \yii\web\Response
     */
    public function actionClearLogs()
    {
        $countLogs = UserSiteActivity::clearHistoryLogs();
        Yii::$app->session->setFlash('success', 'Removed ' . $countLogs . ' logs');
        return $this->redirect(['index']);
    }
}
