<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\search\LeadSearch;
use common\models\Employee;
use common\models\KpiHistory;
use common\models\search\KpiHistorySearch;

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
                        'actions' => ['index', 'view','details'],
                        'allow' => true,
                        'roles' => ['supervision'],
                    ],
                    [
                        'actions' => ['view','details'],
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
        $salary = null;
        $salaryBy = '';
        $searchModel = new LeadSearch();
        $kpiHistory = new KpiHistory();

        $params = Yii::$app->request->queryParams;
        $params2 = Yii::$app->request->post();

        $params = array_merge($params, $params2);

        $dataProvider = $searchModel->searchSoldKpi($params);
        $emplyee_id = null;

        if(isset($params['LeadSearch']['employee_id'])){
            $emplyee_id = $params['LeadSearch']['employee_id'];
            $employee = Employee::findOne(['id' => $emplyee_id]);
        }

        if($employee){
            $historyParams = $employee->paramsForSalary();

            if((!isset($params['LeadSearch']['sold_date_from']) && !isset($params['LeadSearch']['sold_date_to'])) ||
                (empty($params['LeadSearch']['sold_date_from']) && empty($params['LeadSearch']['sold_date_to']))){
                    $start = new \DateTime();
                    $end = new \DateTime();
                    $start->modify('first day of this month');
                    $end->modify('last day of this month');
                    $salaryBy = $start->format('M Y');
            }else{
                if(!empty($params['LeadSearch']['sold_date_from'])){
                    $start = \DateTime::createFromFormat('d-M-Y', $params['LeadSearch']['sold_date_from']);
                }else{
                    $start = null;
                }
                if(!empty($params['LeadSearch']['sold_date_to'])){
                    $end = \DateTime::createFromFormat('d-M-Y', $params['LeadSearch']['sold_date_to']);
                }else{
                    $end = null;
                }

                $today = new \DateTime();
                if($start !== null && $end !== null){
                    $salaryBy = "(".$start->format('j M').' - '.$end->format('j M Y').')';
                }elseif($start !== null){
                    $salaryBy =  "(".$start->format('j M').' - '.$today->format('j M Y').')';
                }elseif($end !== null){
                    $salaryBy =  '(till '.$end->format('j M Y').')';
                }
            }

            $salary = $employee->calculateSalaryBetween($start, $end);

            $kpiHistory->kh_base_amount = $salary['base'];
            $kpiHistory->kh_bonus_active = $historyParams['bonus_active'];
            $kpiHistory->kh_commission_percent = $historyParams['commission_percent'];
            $kpiHistory->kh_user_id = $emplyee_id;
            $kpiHistory->kh_profit_bonus = $salary['bonus'];
            $kpiHistory->kh_estimation_profit = $salary['startProfit'];
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'historyParams' => $historyParams,
            'salary' => $salary,
            'salaryBy' => $salaryBy,
            'kpiHistory' => $kpiHistory,
        ]);
    }

    /**
     * @return mixed
     */
    public function actionView()
    {
        $isAgent = (Yii::$app->authManager->getAssignment('agent', Yii::$app->user->id));
        $searchModel = new KpiHistorySearch();
        $params = Yii::$app->request->queryParams;
        $params2 = Yii::$app->request->post();

        $params = array_merge($params, $params2);
        if($isAgent) {
            $params['KpiHistorySearch']['kh_user_id'] = Yii::$app->user->id;
        }
        $dataProvider = $searchModel->search($params);

        return $this->render('view', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => $isAgent,
        ]);
    }

    /**
     * @return mixed
     */
    public function actionDetails($id)
    {
        $isAgent = (Yii::$app->authManager->getAssignment('agent', Yii::$app->user->id));

        $kpiHistory = KpiHistory::find()->where(['kh_id' => $id])->one();
        if(!$kpiHistory || ($isAgent && Yii::$app->user->id != $kpiHistory->kh_user_id) ){
            return $this->redirect([
                'kpi/view',
            ]);
        }

        $end = new \DateTime($kpiHistory->kh_date_dt);
        $start = clone $end;
        $start->modify('first day of this month');
        $agent = $kpiHistory->khUser;

        if(Yii::$app->request->isPost){
            $postParams = Yii::$app->request->post();
            if(isset($postParams['recalculate_kpi'])){
                $kpiHistory = KpiHistory::recalculateSalary($agent, $start, $end);
                $kpiHistory->kh_agent_approved_dt = null;
                $kpiHistory->kh_super_approved_dt = null;
            }elseif(isset($postParams['approved_by_super'])){
                $kpiHistory->kh_super_approved_dt = date('Y-m-d H:i:s');
                $kpiHistory->kh_super_id = Yii::$app->user->id;
            }elseif(isset($postParams['approved_by_agent'])){
                $kpiHistory->kh_agent_approved_dt = date('Y-m-d H:i:s');
            }else{
                $kpiHistory->load($postParams);
                $kpiHistory->kh_agent_approved_dt = null;
                $kpiHistory->kh_super_approved_dt = null;
            }

            $kpiHistory->save();
        }

        $searchModel = new LeadSearch();
        $params = ['LeadSearch' => [
            'sold_date_from' => $start->format('Y-m-d'),
            'sold_date_to' => $end->format('Y-m-d'),
            'employee_id' => $kpiHistory->kh_user_id
        ]];
        $dataProvider = $searchModel->searchSoldKpi($params);

        return $this->render('details', [
            'kpiHistory' => $kpiHistory,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => $isAgent,
            'month' => $end->format('M-Y'),
            'agent' => $kpiHistory->khUser->username
        ]);
    }
}
