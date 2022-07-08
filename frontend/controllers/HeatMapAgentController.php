<?php

namespace frontend\controllers;

use modules\featureFlag\FFlag;
use modules\shiftSchedule\src\reports\HeatMapAgentSearch;
use modules\shiftSchedule\src\reports\HeatMapAgentService;
use Yii;
use yii\web\BadRequestHttpException;

class HeatMapAgentController extends FController
{
    /**
     * @throws \Exception
     */
    public function actionIndex(): string
    {
        /** @fflag FFlag::FF_KEY_HEAT_MAP_AGENT_REPORT_ENABLE, Heat Map Agent Report enable\disable */
        if (!Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_HEAT_MAP_AGENT_REPORT_ENABLE)) {
            throw new BadRequestHttpException('Feature Flag ' . FFlag::FF_KEY_HEAT_MAP_AGENT_REPORT_ENABLE . ' disable');
        }

        $searchModel = new HeatMapAgentSearch(\Yii::$app->user->identity->timezone);
        $resultHeatMap = $searchModel->eventCountHeatMap(\Yii::$app->request->queryParams);
        $headMapAgentService = (new HeatMapAgentService($resultHeatMap));
        $headMapAgentService->mapResult($searchModel->getFromDT(), $searchModel->getToDT(), $searchModel->timeZone);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'result' => $headMapAgentService->getEventCount(),
            'maxCnt' => $headMapAgentService->getMaxEventCount(),
            'resultByHour' => $headMapAgentService->getEventCountByHour(),
            'maxCntByHour' => $headMapAgentService->getMaxEventCountByHour(),
            'resultByMonthDay' => $headMapAgentService->getEventCountByMonthDay(),
            'maxCntByMonthDay' => $headMapAgentService->getMaxEventCountByMonthDay()
        ]);
    }
}
