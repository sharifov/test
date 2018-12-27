<?php
namespace console\controllers;

use yii\console\Controller;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use common\models\Employee;
use common\models\KpiHistory;

class KpiController extends Controller
{
    /**
     * Calculate salary by month
     *
     */
    public function actionCalculateSalary($dateParam = null)
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        $date = new \DateTime($dateParam);
        if($date->format('d') == 1){
            $date->sub(new \DateInterval('P1D'));
        }

        $start = clone $date;
        $start->modify('first day of this month');
        $end = clone $date;
        $end->modify('last day of this month');

        printf("\nCalculates salary for period ".$start->format('Y-m-d').' '.$end->format('Y-m-d')."\n");

        $agents = Employee::getAllEmployeesByRole('agent');

        foreach ($agents as $agent){
            $salary = $agent->calculateSalaryBetween($start, $end);
            $salaryParams = $agent->paramsForSalary();

            $khDate = $end->format('Y-m-d');
            $khUserId = $agent->id;

            $kpiHistory = KpiHistory::find()->where(['kh_date_dt' => $khDate, 'kh_user_id' => $khUserId])->one();
            if(!$kpiHistory){
                $kpiHistory = new KpiHistory();
                $kpiHistory->kh_date_dt = $khDate;
                $kpiHistory->kh_user_id = $khUserId;
            }

            if(empty($kpiHistory->kh_agent_approved_dt) && empty($kpiHistory->kh_super_approved_dt)){
                $kpiHistory->kh_base_amount = $salaryParams['base_amount'];
                $kpiHistory->kh_commission_percent = $salaryParams['commission_percent'];
                $kpiHistory->kh_bonus_active = $salaryParams['bonus_active'];
                $kpiHistory->kh_profit_bonus = $salary['bonus'];
                $kpiHistory->kh_estimation_profit = $salary['startProfit'];
            }

            if(!$kpiHistory->save()){
                printf("\nSalary for agent ".$agent->username.' not saved');
                print_r($kpiHistory->errors);
            }else{
                print("\nSalary for ".$agent->username.': $'.$salary['salary']);
            }
        }

        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }
}