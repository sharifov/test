<?php

namespace modules\smartLeadDistribution\controllers;

use frontend\controllers\FController;
use modules\smartLeadDistribution\src\entities\LeadRatingReportSearch;
use Yii;

class LeadRatingReportController extends FController
{
    public function actionIndex(): string
    {
        $searchModel = new LeadRatingReportSearch();

        $reportDataList = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'reportDataList' => $reportDataList,
            'searchModel' => $searchModel
        ]);
    }
}
