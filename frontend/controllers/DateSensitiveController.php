<?php

namespace frontend\controllers;

use common\models\DateSensitive;
use src\helpers\app\AppHelper;
use src\services\dateSensitive\DateSensitiveService;
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
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException|\yii\db\Exception
     */
    public function actionCreateView($id)
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
    public function actionDropView($id)
    {
        $dateSensitive = $this->findModel($id);

        $this->dateSensitiveService->dropViews($dateSensitive);
        \Yii::$app->session->setFlash('success', 'Success');

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
}
