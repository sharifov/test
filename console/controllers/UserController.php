<?php

namespace console\controllers;

use common\models\query\EmployeeQuery;
use common\models\UserConnection;
use common\models\UserOnline;
use sales\helpers\setting\SettingHelper;
use sales\model\user\entity\monitor\UserMonitor;
use sales\model\userData\entity\UserDataKey;
use sales\model\userData\entity\UserDataQuery;
use sales\model\userStatDay\entity\UserStatDay;
use yii\console\Controller;
use yii\db\Query;
use yii\helpers\Console;
use yii\helpers\VarDumper;

/**
 * Class UserController
 * @package console\controllers
 *
 */
class UserController extends Controller
{
    /**
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionUpdateOnlineStatus(): void
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        $timeStart = microtime(true);
        $subQuery = UserConnection::find()->select(['DISTINCT(uc_user_id)']);
        $userOnlineForDelete = UserOnline::find()->where(['NOT IN', 'uo_user_id', $subQuery])->all();
        if ($userOnlineForDelete) {
            foreach ($userOnlineForDelete as $item) {
                echo ' - ' . $item->uo_user_id . PHP_EOL;
                $item->delete();
            }
        }

        $userOnline = UserOnline::find()->all();
        if ($userOnline) {
            foreach ($userOnline as $item) {
                $isUserIdle = UserMonitor::isUserIdle($item->uo_user_id);
                if ((bool) $item->uo_idle_state !== $isUserIdle) {
                    $previousAttributes = $item->getAttributes();
                    $item->uo_idle_state = $isUserIdle;
                    $item->uo_idle_state_dt = date('Y-m-d H:i:s');
                    $result = $item->update();
                    if ($result === false) {
                        \Yii::error([
                            'message' => 'UserOnlineStatus. Validation error',
                            'errors' => $item->getErrors(),
                            'attributes' => $item->getAttributes(),
                        ], 'UserController:actionUpdateOnlineStatus:UserOnline:update');
                    } elseif ($result === 0) {
                        // todo change to info level
//                        \Yii::error([
//                            'message' => 'UserOnlineStatus. Record not updated',
//                            'previousAttributes' => $previousAttributes,
//                            'attributes' => $item->getAttributes(),
//                        ], 'UserController:actionUpdateOnlineStatus:UserOnline:update');
                    }
                    echo ' - ' . $item->uo_user_id . ' : ' . ($isUserIdle ? 'idle' : 'active') . PHP_EOL;
                }
            }
        }

        $timeEnd = number_format(round(microtime(true) - $timeStart, 2), 2);
        $resultInfo = ' -- Execute Time: ' . $timeEnd;
        echo $resultInfo;
        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    public function actionCalculateGrossProfit(): void
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;
        $processedUserStatDay = 0;
        $processedUserData = 0;
        $timeStart = microtime(true);

        $date = new \DateTimeImmutable('-1 day');

        $query = new Query();
        $subQuery = EmployeeQuery::getSalesQuery($date->format('Y-m-d 00:00:00'), $date->format('Y-m-d 23:59:59'));
        $query->from(['gross_profit_query' => $subQuery]);
        $query->select([
                'gross_profit' => 'sum(gross_profit)',
                'employee_id'
            ]);
        $query->groupBy(['employee_id']);

        $result = $query->all();
        $userStatDayErrors = [];
        foreach ($result as $userGrossProfit) {
            $userStatDay = UserStatDay::createGrossProfit(
                $userGrossProfit['gross_profit'],
                $userGrossProfit['employee_id'],
                (int)$date->format('d'),
                (int)$date->format('m'),
                (int)$date->format('Y')
            );
            if (!$userStatDay->save()) {
                $userStatDayErrors[] = $userStatDay->getErrorSummary(true)[0];
            }
            $processedUserStatDay++;
        }

        if ($userStatDayErrors) {
            \Yii::error('Saving user_stat_day row failed while calculating users gross profit: ' . PHP_EOL . VarDumper::dumpAsString($userStatDayErrors), 'console:UserController:actionCalculateGrossProfit:userStatDay:save');
        }

        $dateNow = new \DateTimeImmutable();
        $datePeriod = $dateNow->modify('-' . SettingHelper::getCalculateGrossProfitInDays() . ' days');

        $query = new Query();
        $subQuery = EmployeeQuery::getSalesQuery($datePeriod->format('Y-m-d 00:00:00'), $date->format('Y-m-d 23:59:59'));
        $query->from(['gross_profit_query' => $subQuery]);
        $query->select([
            'gross_profit' => 'sum(gross_profit)',
            'employee_id'
        ]);
        $query->groupBy(['employee_id']);

        $result = $query->all();

        $userGrossProfitPeriodErrors = [];
        foreach ($result as $userGrossProfit) {
            $insertOrUpdateResult = UserDataQuery::insertOrUpdate((int)$userGrossProfit['employee_id'], UserDataKey::GROSS_PROFIT, (string)$userGrossProfit['gross_profit'], $dateNow);
            if (!$insertOrUpdateResult) {
                $userGrossProfitPeriodErrors[] = 'Save user data failed with data: ' . VarDumper::dumpAsString([
                    'employeeId' => $userGrossProfit['employee_id'],
                    'key' => UserDataKey::GROSS_PROFIT,
                    'gross_profit' => $userGrossProfit['gross_profit'],
                    'updatedDt' => $dateNow->format('Y-m-d H:i:s')
                ]);
            }
            $processedUserData++;
        }
        if ($userGrossProfitPeriodErrors) {
            \Yii::error('Saving user_data row failed while calculating users gross profit: ' . PHP_EOL . VarDumper::dumpAsString($userGrossProfitPeriodErrors), 'console:UserController:actionCalculateGrossProfit:userStatDay:save');
        }

        $timeEnd = microtime(true);
        $time = number_format(round($timeEnd - $timeStart, 2), 2);
        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time .
            ' s] %g Processed UserStatDay: %w[' . $processedUserStatDay . '] %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time .
            ' s] %g Processed UserData: %w[' . $processedUserData . '] %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;
    }
}
