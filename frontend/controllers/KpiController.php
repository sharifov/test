<?php

namespace frontend\controllers;

use common\models\Employee;
use sales\access\EmployeeGroupAccess;
use sales\auth\Auth;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use common\models\search\LeadSearch;
use common\models\KpiHistory;
use common\models\search\KpiHistorySearch;
use yii\base\DynamicModel;
use common\components\KpiService;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * KpiController.
 */
class KpiController extends FController
{

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        $isAgent = $user->isAgent();

        $searchModel = new KpiHistorySearch();
        $params = Yii::$app->request->queryParams;
        $params2 = Yii::$app->request->post();

        $model = new DynamicModel(['date_dt']);
        $model->addRule(['date_dt'], 'required');

        if($model->load($params2)){
            $date = \DateTime::createFromFormat('M-Y', $params2['DynamicModel']['date_dt']);


            //echo $date; exit;

            $result = KpiService::calculateSalary($date->format('Y-m-d'));

            Yii::info('Month: '.$date->format('M-Y').' User: '.Yii::$app->user->id.' Agents: All ', 'info\KpiService::calculateSalary');
            Yii::$app->session->setFlash('success', 'Calculate Salary Month: '.$date->format('M-Y'));

            return $this->redirect([
                'kpi/index',
                'KpiHistorySearch[kh_date_dt]' => $params2['DynamicModel']['date_dt'],
            ]);
        }

        $params = array_merge($params, $params2);
        if($isAgent) {
            $params['KpiHistorySearch']['kh_user_id'] = $user->id;
        } elseif ($user->isSupervision()) {
            $userIds = EmployeeGroupAccess::getUsersIdsInCommonGroups($user->id);
            $params['KpiHistorySearch']['usersIdsInCommonGroups'] = array_keys($userIds);
        }
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => $isAgent,
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws \Exception
     */
    public function actionDetails($id)
    {
        $isAgent = Auth::user()->isAgent();

        $kpiHistory = KpiHistory::find()->where(['kh_id' => $id])->one();
        if(!$kpiHistory || ($isAgent && Yii::$app->user->id !== $kpiHistory->kh_user_id) ){
            return $this->redirect([
                'kpi/index',
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
                Yii::info('Month: '.$end->format('M-Y').' User: '.Yii::$app->user->id.' Agent: '.$agent->id, 'info\KpiHistory::recalculateSalary');

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
