<?php
namespace common\components;

use common\models\Employee;
use common\models\KpiHistory;

class KpiService
{

    public static function calculateSalary($dateParam = null)
    {
        $errors = [];
        $messages = [];

        $date = new \DateTime($dateParam);
        if($date->format('d') == 1){
            $date->sub(new \DateInterval('P1D'));
        }
        $start = clone $date;
        $start->modify('first day of this month');
        $end = clone $date;
        $end->modify('last day of this month');

        $agents = Employee::getAllEmployeesByRole('agent');

        foreach ($agents as $agent){
            $kpiHistory = KpiHistory::recalculateSalary($agent, $start, $end);
            if(!$kpiHistory->save()){
                $errors[] = $kpiHistory->errors;
            }else{
                $messages[] = "Salary for ".$agent->username.': $'.$kpiHistory->getSalary();
            }
        }

        return ['start' => $start, 'end' => $end, 'errors' => $errors, 'messages' => $messages];

    }
}