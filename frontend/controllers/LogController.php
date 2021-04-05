<?php

namespace frontend\controllers;

use aki\telegram\base\Command;
use Mpdf\Tag\Li;
use sales\auth\Auth;
use sales\helpers\app\AppHelper;
use sales\helpers\ErrorsToStringHelper;
use sales\services\cleaner\cleaners\LogCleaner;
use sales\services\cleaner\DbCleanerService;
use sales\services\cleaner\form\DbCleanerParamsForm;
use sales\services\cleaner\form\LogCleanerForm;
use Yii;
use frontend\models\Log;
use frontend\models\search\LogSearch;
use yii\db\Query;
use yii\db\QueryBuilder;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * LogController implements the CRUD actions for Log model.
 */
class LogController extends FController
{

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

    /**
     * Lists of Log Category.
     * @return array
     */
    public function actionAjaxCategoryList(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['error' => '', 'data' => []];
        $result['data'] = Log::getCategoryFilterByCnt();
        return $result;
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionCleanTable(): array
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $result = ['message' => '', 'status' => 0];

            try {
                if (!Auth::can('global/clean/table')) {
                    throw new ForbiddenHttpException('You don\'t have access to this page', -1);
                }

                $form = new LogCleanerForm();
                if (!$form->load(Yii::$app->request->post())) {
                    throw new BadRequestHttpException('Form not loaded from post request', -2);
                }
                if (!$form->validate()) {
                    throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($form), -3);
                }

                $restrictionDt = DbCleanerService::generateRestrictionTimestamp($form);
                $query = (new Query())->select(['id'])
                    ->from(Log::tableName())
                    ->where($restrictionDt);
                if ($form->category) {
                    $query->andWhere(['category' => $form->category]);
                }
                if ($form->level) {
                    $query->andWhere(['level' => $form->level]);
                }

                $sql = $query->createCommand()->getRawSql();
                $sql = LogCleaner::replaceSelectToDelete($sql);
                $processed = Log::getDb()->createCommand($sql)->execute();

                if ($processed) {
                    $message = 'Processed ' . $processed . ' records';
                } else {
                    $message = 'No records found matching the specified criteria';
                }

                $result['message'] = $message;
                $result['status'] = 1;
            } catch (\Throwable $throwable) {
                AppHelper::throwableLogger(
                    $throwable,
                    'LogController:actionCleanTable:throwable'
                );
                $result['message'] = VarDumper::dumpAsString($throwable->getMessage());
            }
            return $result;
        }
        throw new BadRequestHttpException();
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
        Log::getDb()->createCommand()->truncateTable('log')->execute();
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
