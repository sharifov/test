<?php
namespace console\controllers;

use yii\console\Controller;
use yii\helpers\Console;
use common\components\KpiService;

class KpiController extends Controller
{
    /**
     * Calculate salary by month
     *
     */
    public function actionCalculateSalary($dateParam = null)
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        $result = KpiService::calculateSalary($dateParam);

        printf($this->ansiFormat("\nCalculates salary for period ".$result['start']->format('Y-m-d').' '.$result['end']->format('Y-m-d')."\n", Console::FG_BLUE));

        if(count($result['errors'])){
            printf($this->ansiFormat("\n --- Errors ---\n" , Console::FG_RED));
            foreach ($result['errors'] as $error){
                print_r($error);
            }
        }

        if(count($result['messages'])){
            printf($this->ansiFormat("\n --- Info ---\n" , Console::FG_BLUE));
            foreach ($result['messages'] as $message){
                print("\n".$message);
            }
        }

        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }
}