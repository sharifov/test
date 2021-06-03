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
    private const CACHE_DURATION = 60 * 30;

    public function actionIndex()
    {
        set_time_limit(30);
        $cacheDuration = Yii::$app->request->get('debug') ? -1 : self::CACHE_DURATION;

        $searchModel = new SalesSearch(Auth::user());
        $dataProvider = $searchModel->searchByUser(Yii::$app->request->queryParams, $cacheDuration);
        $query = clone $dataProvider->query;

        $totalCount = $dataProvider->totalCount;
        $sumGrossProfit = array_sum(array_column($query->all(), 'gross_profit'));

        $qualifiedLeadsTakenQuery = $searchModel->qualifiedLeadsTakenQuery(Yii::$app->request->queryParams, -1);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'totalCount' => $totalCount,
            'sumGrossProfit' => $sumGrossProfit,
            'qualifiedLeadsTaken' => $qualifiedLeadsTakenQuery->count(),
            'cacheDuration' => $cacheDuration
        ]);
    }
}
