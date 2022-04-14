<?php

namespace frontend\controllers;

use common\models\Project;
use common\models\Sources;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use src\auth\Auth;
use src\model\lead\reports\HeatMapLeadSearch;
use src\model\lead\reports\HeatMapLeadService;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;

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
        $leadAbacDto = new LeadAbacDto(null, (int) Auth::id());
        /** @abac $leadAbacDto, LeadAbacObject::OBJ_HEAT_MAP_LEAD, LeadAbacObject::ACTION_ACCESS, access to actionIndex */
        $canView = \Yii::$app->abac->can($leadAbacDto, LeadAbacObject::OBJ_HEAT_MAP_LEAD, LeadAbacObject::ACTION_ACCESS);

        if (!$canView) {
            throw new ForbiddenHttpException('Access denied.');
        }

        $searchModel = new HeatMapLeadSearch(\Yii::$app->user->identity->timezone);
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
