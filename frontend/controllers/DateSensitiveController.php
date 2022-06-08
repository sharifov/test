<?php

namespace frontend\controllers;

use common\models\DateSensitive;
use common\models\DateSensitiveView;
use src\helpers\app\AppHelper;
use src\services\dateSensitive\DateSensitiveService;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class DateSensitiveController extends FController
{
    private DateSensitiveService $dateSensitiveService;

    public function __construct($id, $module, DateSensitiveService $dateSensitiveService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->dateSensitiveService = $dateSensitiveService;
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
        $dateSensitive = $this->findModel($id);
        $this->dateSensitiveService->createViews($dateSensitive);
        \Yii::$app->session->setFlash('success', 'Success');
        return $this->redirect(\Yii::$app->request->referrer);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDropViews($id)
    {
        $dateSensitive = $this->findModel($id);
        $this->dateSensitiveService->dropViews($dateSensitive);
        \Yii::$app->session->setFlash('success', 'Success');
        return $this->redirect(\Yii::$app->request->referrer);
    }


    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDropView($viewName)
    {
        $dateSensitiveView = $this->findDateSensitiveView($viewName);
        try {
            $this->dateSensitiveService->dropViewByDateSensitiveView($dateSensitiveView);
            \Yii::$app->session->setFlash('success', 'Success');
        } catch (\RuntimeException | \DomainException $e) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($e), ['DateSensitiveViewName' => $viewName]);
            \Yii::warning($message, 'DateSensitiveController:actionDropView:Exception');
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['DateSensitiveViewName' => $viewName]);
            \Yii::error($message, 'DateSensitiveController:actionDropView:Throwable');
        }

        return $this->redirect(\Yii::$app->request->referrer);
    }

    /**
     * @param $id
     * @return DateSensitive|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = DateSensitive::findOne(['da_id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Date Sensitive not found by ID(' . $id . ')');
    }

    /**
     * @param $viewName
     * @return DateSensitiveView|null
     * @throws NotFoundHttpException
     */
    protected function findDateSensitiveView($viewName)
    {
        if (($model = DateSensitiveView::findOne(['dv_view_name' => $viewName])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Date Sensitive View not found by View Name(' . $viewName . ')');
    }
}
