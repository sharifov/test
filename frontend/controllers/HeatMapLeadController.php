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

    public function actionIndex(): string
    {
        $searchModel = new HeatMapLeadSearch();
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
