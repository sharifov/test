<?php

namespace console\controllers;

use common\models\Setting;
use modules\shiftSchedule\src\services\UserShiftScheduleService;
use src\helpers\setting\SettingHelper;
use yii\console\Controller;
use yii\helpers\Console;

class ShiftController extends Controller
{
    /**
     * @return void
     */
    public function actionGenerateUserSchedule(): void
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;
        $timeStart = microtime(true);

        if (SettingHelper::getShiftScheduleGenerateEnabled()) {
            $limit = SettingHelper::getShiftScheduleDaysLimit();
            $offset = SettingHelper::getShiftScheduleDaysOffset();
            $data = UserShiftScheduleService::generateUserSchedule($limit, $offset, null);
        } else {
            echo 'Warning: SiteSetting[shift_schedule][generate_enabled] is not true!' . PHP_EOL;
        }

        $timeEnd = microtime(true);
        $time = number_format(round($timeEnd - $timeStart, 2), 2);
        echo Console::renderColoredString('%g --- Count: %w' . count($data) . '%g, Execute Time: %w[' . $time .
            ' s] %g'), PHP_EOL;
        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;
    }
}
