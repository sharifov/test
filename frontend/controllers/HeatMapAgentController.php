<?php

namespace frontend\controllers;

use modules\shiftSchedule\src\reports\HeatMapAgentSearch;
use yii\helpers\ArrayHelper;

class HeatMapAgentController extends FController
{
    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'allowActions' => [
                    'index',
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }


    public function actionIndex(): string
    {
        $searchModel = new HeatMapAgentSearch(\Yii::$app->user->identity->timezone);
        $resultHeatMap = $searchModel->leadCountHeatMap(\Yii::$app->request->queryParams);
        $mappedResult = HeatMapLeadService::mapResult($resultHeatMap, $searchModel->getFromDT(), $searchModel->getToDT());
        $resultByHour = $searchModel->leadChtByHour(\Yii::$app->request->queryParams);
        $resultByMonthDay = $searchModel->leadChtByMonthDay(\Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'result' => $mappedResult,
            'maxCnt' => HeatMapLeadService::getMaxCnt($resultHeatMap),
            'resultByHour' => $resultByHour,
            'maxCntByHour' => HeatMapLeadService::getMaxCnt($resultByHour),
            'resultByMonthDay' => $resultByMonthDay,
            'maxCntByMonthDay' => HeatMapLeadService::getMaxCnt($resultByMonthDay),
        ]);
    }
}
