<?php

namespace console\controllers;

use common\models\Employee;
use common\models\query\EmployeeQuery;
use common\models\UserConnection;
use common\models\UserOnline;
use common\models\UserParams;
use modules\user\userActivity\entity\UserActivity;
use modules\user\userActivity\service\UserActivityService;
use src\helpers\setting\SettingHelper;
use src\model\leadRedial\priorityLevel\ConversionFetcher;
use src\model\leadRedial\priorityLevel\PriorityLevelCalculator;
use src\model\user\entity\monitor\UserMonitor;
use src\model\userData\entity\UserDataKey;
use src\model\userData\entity\UserDataQuery;
use src\model\userStatDay\entity\UserStatDay;
use yii\console\Controller;
use yii\db\Expression;
use yii\db\IntegrityException;
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

        $query = (new Query())
            ->select([
                'users.id as employee_id',
                'gross_profit' => new Expression('if ((gross_profit is null or gross_profit = 0), 0, gross_profit)')
            ])
            ->from(['users' => Employee::tableName()])
            ->leftJoin([
                'gr_users' => (new Query())
                    ->select([
                        'gross_profit' => 'sum(gross_profit)',
                        'employee_id'
                    ])
                    ->from([
                        'gross_profit_query' => EmployeeQuery::getSalesQuery($date->format('Y-m-d 00:00:00'), $date->format('Y-m-d 23:59:59'))
                    ])
                    ->groupBy(['employee_id'])
            ], 'gr_users.employee_id = users.id')
            ->andWhere(['users.status' => Employee::STATUS_ACTIVE]);
        $result = $query->all();
        $userStatDayErrors = [];
        foreach ($result as $userGrossProfit) {
            $userStatDay = UserStatDay::createOrUpdateGrossProfit(
                (float)$userGrossProfit['gross_profit'],
                $userGrossProfit['employee_id'],
                (int)$date->format('d'),
                (int)$date->format('m'),
                (int)$date->format('Y')
            );
            if (!$userStatDay->validate() || !$userStatDay->save()) {
                $userStatDayErrors[] = $userStatDay->getErrorSummary(true)[0];
            }
            $processedUserStatDay++;
        }

        if ($userStatDayErrors) {
            \Yii::warning('Saving user_stat_day row failed while calculating users gross profit: ' . PHP_EOL . VarDumper::dumpAsString($userStatDayErrors), 'console:UserController:actionCalculateGrossProfit:userStatDay:save');
        }

        $dateNow = new \DateTimeImmutable();
        $to = $dateNow->modify('-1 day');
        $from = $to->modify('-' . SettingHelper::getCalculateGrossProfitInDays() . ' days');
        $query = (new Query())
            ->select([
                'users.id as employee_id',
                'gross_profit' => new Expression('if ((gross_profit is null or gross_profit = 0), 0, gross_profit)')
            ])
            ->from(['users' => Employee::tableName()])
            ->leftJoin([
                'gr_users' => (new Query())
                    ->select([
                        'gross_profit' => 'sum(gross_profit)',
                        'employee_id'
                    ])
                    ->from([
                        'gross_profit_query' => EmployeeQuery::getSalesQuery($from->format('Y-m-d 00:00:00'), $to->format('Y-m-d 23:59:59'))
                    ])
                    ->groupBy(['employee_id'])
            ], 'gr_users.employee_id = users.id')
            ->andWhere(['users.status' => Employee::STATUS_ACTIVE]);
        $result = $query->all();

        $userGrossProfitPeriodErrors = [];
        foreach ($result as $userGrossProfit) {
            try {
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
            } catch (\Throwable $e) {
                $userGrossProfitPeriodErrors[] = 'Save user data failed with data: ' . VarDumper::dumpAsString([
                    'employeeId' => $userGrossProfit['employee_id'],
                    'key' => UserDataKey::GROSS_PROFIT,
                    'gross_profit' => $userGrossProfit['gross_profit'],
                    'updatedDt' => $dateNow->format('Y-m-d H:i:s')
                ]);
            }
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

    public function actionCalculatePriorityLevel(): void
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' . self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

        $timeStart = microtime(true);

        $dateNow = new \DateTimeImmutable();
        $to = $dateNow->modify('-1 day');
        $from = $to->modify('-' . SettingHelper::getCalculatePriorityLevelInDays() . ' days');
        $conversions = (new ConversionFetcher())->fetch($from, $to);
        $priorityLevelCalculator = \Yii::createObject(PriorityLevelCalculator::class);
        $userDataErrors = [];

        foreach ($conversions as $conversion) {
            try {
                UserParams::updateAll(
                    ['up_call_user_level' => $priorityLevelCalculator->calculate($conversion['conversion_percent'])],
                    'up_user_id = :userId',
                    [':userId' => (int)$conversion['user_id']]
                );

                $insertOrUpdateResult = UserDataQuery::insertOrUpdate((int)$conversion['user_id'], UserDataKey::CONVERSION_PERCENT, (string)$conversion['conversion_percent'], $dateNow);
                if (!$insertOrUpdateResult) {
                    $userDataErrors[] = 'Save user data failed with data: ' . VarDumper::dumpAsString([
                            'employeeId' => $conversion['user_id'],
                            'key' => UserDataKey::CONVERSION_PERCENT,
                            'conversion' => $conversion['conversion_percent'],
                            'updatedDt' => $dateNow->format('Y-m-d H:i:s')
                        ]);
                }
            } catch (\Throwable $e) {
                \Yii::error([
                    'message' => 'Update user priority level',
                    'exception' => $e->getMessage(),
                    'userId' => $conversion['user_id'],
                    'conversion_percent' => $conversion['conversion_percent'],
                ], 'UserController:actionCalculatePriorityLevel');
            }
        }

        if ($userDataErrors) {
            \Yii::error('Saving user_data row failed while calculating users conversion: ' . PHP_EOL . VarDumper::dumpAsString($userDataErrors), 'console:UserController:actionCalculatePriorityLevel:userData:save');
        }

        $timeEnd = microtime(true);
        $time = number_format(round($timeEnd - $timeStart, 2), 2);
        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time . ' s] %g %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . '] %g' . self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;
    }

    /**
     * @return void
     */
    public function actionCheckActivity(): void
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' . self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;
        $timeStart = microtime(true);

        $userList = UserActivityService::checkUserActivity();
        if ($userList) {
            foreach ($userList as $userId) {
                echo Console::renderColoredString('%g --- User Id: %w' . $userId . ' %g %n'), PHP_EOL;
            }
        }

        $timeEnd = microtime(true);
        $time = number_format(round($timeEnd - $timeStart, 2), 2);
        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time . ' s] %g %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . '] %g' . self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;
    }
}
