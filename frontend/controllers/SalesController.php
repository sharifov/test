<?php

namespace frontend\controllers;

use common\models\Employee;
use src\auth\Auth;
use src\model\user\entity\sales\SalesSearch;
use Yii;
use yii\data\ArrayDataProvider;

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
        $models = $searchModel->searchByUser(Yii::$app->request->queryParams, $cacheDuration);

        $dataProvider = new ArrayDataProvider([
            'allModels' => $models,
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);
        $dataProvider->setSort([
            'attributes' => [
                'id' => [
                    'asc' => ['id' => SORT_ASC],
                    'desc' => ['id' => SORT_DESC],
                ],
                'l_status_dt' => [
                    'asc' => ['l_status_dt' => SORT_ASC],
                    'desc' => ['l_status_dt' => SORT_DESC],
                ],
                'final_profit' => [
                    'asc' => ['gross_profit' => SORT_ASC],
                    'desc' => ['gross_profit' => SORT_DESC],
                ],
                'created' => [
                    'asc' => ['created' => SORT_ASC],
                    'desc' => ['created' => SORT_DESC],
                ],
            ],
            'defaultOrder' => [
                'l_status_dt' => SORT_DESC
            ]
        ]);

        $totalCount = $dataProvider->totalCount;
        $sumGrossProfit = array_sum(array_column($models, 'gross_profit'));
        $sumShare = array_sum(array_column($models, 'share'));

        $searchQualifiedLeads = $searchModel->searchQualifiedLeads(Yii::$app->request->queryParams, $cacheDuration);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'totalCount' => $totalCount,
            'sumGrossProfit' => $sumGrossProfit,
            'sumShare' => $sumShare,
            'qualifiedLeadsTakenCount' => $searchQualifiedLeads->totalCount,
            'cacheDuration' => $cacheDuration,
            'searchQualifiedLeads' => $searchQualifiedLeads,
        ]);
    }
}
