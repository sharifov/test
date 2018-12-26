<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\search\LeadSearch;
use common\models\Employee;

/**
 * KpiController.
 */
class KpiController extends FController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'roles' => ['supervision'],
                    ],
                    [
                        'actions' => ['view'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }


    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $employee = null;
        $historyParams = [];
        $searchModel = new LeadSearch();

        $params = Yii::$app->request->queryParams;
        $params2 = Yii::$app->request->post();

        $params = array_merge($params, $params2);

        $dataProvider = $searchModel->searchSoldKpi($params);

        if(isset($params['LeadSearch']['employee_id'])){
            $employee = Employee::findOne(['id' => $params['LeadSearch']['employee_id']]);
        }

        if($employee){
            if(!empty($employee->userParams)){
                $historyParams['bonus_active'] = $employee->userParams->up_bonus_active;
                $historyParams['base_amount'] = $employee->userParams->up_base_amount;
                $historyParams['commision_percent'] = $employee->userParams->up_commission_percent;
            }
            $historyParams['profit_bonuses'] = $employee->getProfitBonuses();
            if(empty($historyParams['profit_bonuses'])){
                $historyParams['profit_bonuses'] = $employee::PROFIT_BONUSES;
            }
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'historyParams' => $historyParams,
        ]);
    }

    /**
     * @return mixed
     */
    public function actionView()
    {
        $searchModel = new LeadSearch();
        $dataProvider = $searchModel->searchSold(Yii::$app->request->queryParams);

        return $this->render('view', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
