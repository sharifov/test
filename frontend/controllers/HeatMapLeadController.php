<?php

namespace frontend\controllers;

use src\model\lead\reports\HeatMapLeadSearch;
use src\model\lead\reports\HeatMapLeadService;

/**
 * Class HeatMapLeadController
 */
class HeatMapLeadController extends FController
{
    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    public function actionIndex()
    {
        /* TODO:: cache */

        $searchModel = new HeatMapLeadSearch();
        $resultHeatMap = $searchModel->leadChtHeatMap(\Yii::$app->request->queryParams, 300);
        $mappedResult = HeatMapLeadService::mapResult($resultHeatMap, $searchModel->getFromDT(), $searchModel->getToDT());
        $resultByHour = $searchModel->leadChtByHour(\Yii::$app->request->queryParams, 300);
        $resultByMonthDay = $searchModel->leadChtByMonthDay(\Yii::$app->request->queryParams, 300);

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
