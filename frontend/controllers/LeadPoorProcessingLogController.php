<?php

namespace frontend\controllers;

use common\models\Lead;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use src\auth\Auth;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogSearch;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class LeadPoorProcessingLogController
 */
class LeadPoorProcessingLogController extends FController
{
    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'allowActions' => [
                    'ajax-log'
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionAjaxLog($leadId)
    {
        if (!$lead = Lead::find()->where(['id' => $leadId])->limit(1)->one()) {
            throw new NotFoundHttpException('Lead not found by ID(' . $leadId . ')');
        }

        $leadAbacDto = new LeadAbacDto($lead, (int) Auth::id());
        /** @abac $leadAbacDto, LeadAbacObject::OBJ_EXTRA_QUEUE, LeadAbacObject::ACTION_ACCESS, access to actionAjaxLog */
        $canLeadPoorProcessingLogs = Yii::$app->abac->can($leadAbacDto, LeadAbacObject::OBJ_EXTRA_QUEUE, LeadAbacObject::ACTION_ACCESS);

        if (!$canLeadPoorProcessingLogs) {
            throw new ForbiddenHttpException('Access denied. Lead(' . $leadId . '), User(' . Auth::id() . ')');
        }

        $search = new LeadPoorProcessingLogSearch();
        $dataProvider = $search->search([$search->formName() => [
            'lppl_lead_id' => $leadId
        ]]);
        $dataProvider->pagination = false;
        return $this->renderAjax('ajax-log', [
            'dataProvider' => $dataProvider,
            'model' => $search
        ]);
    }
}
