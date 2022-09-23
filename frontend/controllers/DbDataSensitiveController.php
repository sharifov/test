<?php

namespace frontend\controllers;

use src\model\dbDataSensitive\entity\DbDataSensitive;
use src\model\dbDataSensitiveView\entity\DbDataSensitiveView;
use src\helpers\app\AppHelper;
use src\model\dbDataSensitive\service\DbDataSensitiveService;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class DbDataSensitiveController extends FController
{
    private DbDataSensitiveService $dbDataSensitiveService;

    public function __construct($id, $module, DbDataSensitiveService $dbDataSensitiveService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->dbDataSensitiveService = $dbDataSensitiveService;
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'create-views' => ['POST'],
                        'drop-views' => ['POST'],
                        'drop-view' => ['POST'],
                    ],
                ],
            ]
        );
    }


    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException|\yii\db\Exception
     */
    public function actionCreateViews($id)
    {
        $dbDataSensitive = $this->findModel($id);
        $this->dbDataSensitiveService->createViews($dbDataSensitive);
        \Yii::$app->session->setFlash('success', 'Views creation has been successful');
        return $this->redirect(\Yii::$app->request->referrer);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDropViews($id)
    {
        $dbDataSensitive = $this->findModel($id);
        $this->dbDataSensitiveService->dropViews($dbDataSensitive);
        \Yii::$app->session->setFlash('success', 'Views deletion has been successful');
        return $this->redirect(\Yii::$app->request->referrer);
    }


    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDropView($viewName)
    {
        $dbDataSensitiveView = $this->findDbDataSensitiveView($viewName);
        try {
            $this->dbDataSensitiveService->dropViewByDbDataSensitiveView($dbDataSensitiveView);
            \Yii::$app->session->setFlash('success', 'View deletion has been successful');
        } catch (\RuntimeException | \DomainException $e) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($e), ['DataSensitiveViewName' => $viewName]);
            \Yii::warning($message, 'DbDataSensitiveController:actionDropView:Exception');
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['DataSensitiveViewName' => $viewName]);
            \Yii::error($message, 'DbDataSensitiveController:actionDropView:Throwable');
        }

        return $this->redirect(\Yii::$app->request->referrer);
    }

    /**
     * @param $id
     * @return DbDataSensitive|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = DbDataSensitive::findOne(['dda_id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Data Sensitive not found by ID(' . $id . ')');
    }

    /**
     * @param $viewName
     * @return DbDataSensitiveView|null
     * @throws NotFoundHttpException
     */
    protected function findDbDataSensitiveView($viewName)
    {
        if (($model = DbDataSensitiveView::findOne(['ddv_view_name' => $viewName])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Data Sensitive View not found by View Name(' . $viewName . ')');
    }
}
