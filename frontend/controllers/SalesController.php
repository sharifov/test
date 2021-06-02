<?php

namespace frontend\controllers;

use sales\auth\Auth;
use sales\helpers\query\QueryHelper;
use sales\model\user\entity\sales\SalesSearch;
use Yii;

/**
 * Class SalesController
 */
class SalesController extends FController
{
    public function actionIndex()
    {
        set_time_limit(30);
        if (isset(Yii::$app->log->targets['debug']->enabled)) {
            Yii::$app->log->targets['debug']->enabled = false;
        }

        $searchModel = new SalesSearch(Auth::user());
        $dataProvider = $searchModel->searchByUser(Yii::$app->request->queryParams);
        $query = clone $dataProvider->query;

        $totalCount = $dataProvider->totalCount;
        $sumGrossProfit = array_sum(array_column($query->all(), 'gross_profit'));

        $qualifiedLeadsTakenQuery = $searchModel->qualifiedLeadsTakenQuery(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'totalCount' => $totalCount,
            'sumGrossProfit' => $sumGrossProfit,
            'qualifiedLeadsTaken' => $qualifiedLeadsTakenQuery->count(),
        ]);
    }
}
