<?php

namespace frontend\controllers;

use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogSearch;

class LeadPoorProcessingLogController extends FController
{
    public function actionAjaxLog($leadId)
    {
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
