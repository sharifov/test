<?php

namespace frontend\controllers;

use common\models\DbDateSensitive;
use common\models\DbDateSensitiveView;
use src\helpers\app\AppHelper;
use src\services\dbDateSensitive\DbDateSensitiveService;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class DbDateSensitiveController extends FController
{
    private DbDateSensitiveService $dbDateSensitiveService;

    public function __construct($id, $module, DbDateSensitiveService $dbDateSensitiveService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->dbDateSensitiveService = $dbDateSensitiveService;
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
        $dbDateSensitive = $this->findModel($id);
        $this->dbDateSensitiveService->createViews($dbDateSensitive);
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
        $dbDateSensitive = $this->findModel($id);
        $this->dbDateSensitiveService->dropViews($dbDateSensitive);
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
        $dbDateSensitiveView = $this->findDbDateSensitiveView($viewName);
        try {
            $this->dbDateSensitiveService->dropViewByDbDateSensitiveView($dbDateSensitiveView);
            \Yii::$app->session->setFlash('success', 'View deletion has been successful');
        } catch (\RuntimeException | \DomainException $e) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($e), ['DateSensitiveViewName' => $viewName]);
            \Yii::warning($message, 'DbDateSensitiveController:actionDropView:Exception');
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['DateSensitiveViewName' => $viewName]);
            \Yii::error($message, 'DbDateSensitiveController:actionDropView:Throwable');
        }

        return $this->redirect(\Yii::$app->request->referrer);
    }

    /**
     * @param $id
     * @return DbDateSensitive|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = DbDateSensitive::findOne(['dda_id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Date Sensitive not found by ID(' . $id . ')');
    }

    /**
     * @param $viewName
     * @return DbDateSensitiveView|null
     * @throws NotFoundHttpException
     */
    protected function findDbDateSensitiveView($viewName)
    {
        if (($model = DbDateSensitiveView::findOne(['ddv_view_name' => $viewName])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Date Sensitive View not found by View Name(' . $viewName . ')');
    }
}
