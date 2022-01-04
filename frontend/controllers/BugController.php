<?php

namespace frontend\controllers;

use common\models\Employee;
use sales\auth\Auth;
use sales\services\cleaner\cleaners\LogCleaner;
use sales\services\cleaner\form\LogCleanerForm;
use Yii;
use frontend\models\Log;
use frontend\models\search\LogSearch;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * BugController implements the CRUD actions for Log model.
 */
class BugController extends FController
{

    public function behaviors()
    {
        $behaviors = [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'roles' => [
                            Employee::ROLE_ADMIN,
                            Employee::ROLE_AGENT,
                        ],
                        'allow' => true,
                    ],
                ],
            ],
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
     * Lists all Log models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LogSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $cleaner = new LogCleaner();
        $logCleanerForm = (new LogCleanerForm())
            ->setTable($cleaner->getTable())
            ->setColumn($cleaner->getColumn());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'modelCleaner' => $logCleanerForm,
        ]);
    }


    public function actionCreate()
    {
        /*$searchModel = new LogSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $cleaner = new LogCleaner();
        $logCleanerForm = (new LogCleanerForm())
            ->setTable($cleaner->getTable())
            ->setColumn($cleaner->getColumn());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'modelCleaner' => $logCleanerForm,
        ]);*/
        return 'aaaaaa';
    }


    /**
     * Lists of Log Category.
     * @return array
     */
    public function actionAjaxCategoryList(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['error' => '', 'data' => []];
        $result['data'] = Log::getCategoryFilterByCnt(null, false);
        return $result;
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {

        $model = $this->findModel($id);

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', ['model' => $model]);
        } else {
            return $this->render('view', ['model' => $model]);
        }
    }

    /**
     * Deletes an existing Log model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    /**
     * Truncate table.
     */
    public function actionClear()
    {
        if (!Auth::can('global/clean/table')) {
            throw new ForbiddenHttpException('You don\'t have access to this page');
        }
        Log::getDb()->createCommand()->truncateTable(Log::tableName())->execute();
        $this->redirect(['log/index']);
    }

    /**
     * Finds the Log model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Log the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Log::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
