<?php

namespace frontend\controllers;

use common\models\Employee;
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
        $debug = Yii::$app->request->get('debug');
        $userId = (int) Yii::$app->request->get('user_id', 0);
        $cacheDuration = $debug ? -1 : self::CACHE_DURATION;
        $employee = Auth::user();

        if ($debug && ($user = Employee::findOne($userId)) && ($employee->isAdmin() || $employee->isSuperAdmin())) {
            $employee = $user;
        }

        $searchModel = new SalesSearch($employee);
        $dataProvider = $searchModel->searchByUser(Yii::$app->request->queryParams, $cacheDuration);
        $query = clone $dataProvider->query;

        $totalCount = $dataProvider->totalCount;
        $sumGrossProfit = array_sum(array_column($query->all(), 'gross_profit'));

        $searchQualifiedLeads = $searchModel->searchQualifiedLeads(Yii::$app->request->queryParams, $cacheDuration);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'totalCount' => $totalCount,
            'sumGrossProfit' => $sumGrossProfit,
            'qualifiedLeadsTakenCount' => $searchQualifiedLeads->totalCount,
            'cacheDuration' => $cacheDuration,
            'searchQualifiedLeads' => $searchQualifiedLeads,
        ]);
    }
}
