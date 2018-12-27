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
            $kpiHistory = KpiHistory::recalculateSalary($agent, $start, $end);
            if(!$kpiHistory->save()){
                printf("\nSalary for agent ".$agent->username.' not saved');
                print_r($kpiHistory->errors);
            }else{
                print("\nSalary for ".$agent->username.': $'.$kpiHistory->getSalary());
            }
        }

        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }
}