<?php

namespace console\controllers;

use modules\user\src\events\UserEvents;
use modules\user\userActivity\entity\UserActivity;
use modules\user\userActivity\service\UserActivityService;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use yii\helpers\VarDumper;

/**
 * Class UserActivityController
 */
class UserActivityController extends Controller
{
    public function actionGenerateRand(int $userId, ?string $startDate = null, ?string $endDate = null): int
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

        $timeStart = microtime(true);

        if (!$startDate) {
            $startDate = date('Y-m-d');
        }

        if (!$endDate) {
            $endDate = date('Y-m-d');
        }

        $dateFromTs = strtotime($startDate);
        $dateToTs = strtotime($endDate);

//        $m = (int) date("m", $dateFromTs);
//        $d = (int) date("d", $dateFromTs);
//        $y = (int) date("Y", $dateFromTs);
//
//        $m2 = (int) date("m", $dateToTs);
//        $d2 = (int) date("d", $dateToTs);
//        $y2 = (int) date("Y", $dateToTs);


        $minutes = [0, 10, 20, 30, 40];

        $data = [];

        for ($time = $dateFromTs; $time <= $dateToTs; $time += (60 * 60 * 24)) {
            $m = (int) date("m", $time);
            $d = (int) date("d", $time);
            $y = (int) date("Y", $time);


            $hour = random_int(6, 13);
            $min = $minutes[random_int(1, count($minutes)) - 1];

            $timeStart = mktime($hour, $min, 0, $m, $d, $y);
            $duration = random_int(6, 8) * 60 + $minutes[random_int(1, count($minutes)) - 1];
            $timeEnd = mktime($hour, $duration + $min, 0, $m, $d, $y);

            for ($timeMinutes = $timeStart; $timeMinutes <= $timeEnd; $timeMinutes += 60) {
                $dateTime = date('Y-m-d H:i:s', $timeMinutes); //mktime($hour, $min, 0, $m2, $d2, $y2);
                $data[] = $dateTime;

                echo Console::renderColoredString('%g ---  : %w' . $dateTime . '%n'), PHP_EOL;

                UserActivityService::addEvent(
                    (int) $userId,
                    UserEvents::EVENT_ONLINE,
                    $dateTime,
                    null,
                    $dateTime,
                    null,
                    UserActivity::TYPE_MONITORING
                );
            }
        }

        //VarDumper::dump($data);

        $processed = count($data);
        $timeEnd = microtime(true);
        $time = number_format(round($timeEnd - $timeStart, 2), 2);
        self::outputResult($processed, $time);
        return ExitCode::OK;
    }


    /**
     * @param $processed
     * @param $time
     */
    private static function outputResult(
        $processed,
        $time
    ): void {
        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time .
            ' s] %g Processed: %w[' . $processed . '] %g %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . ']%n'), PHP_EOL;

        Yii::info(VarDumper::dumpAsString([
            'Processed' => $processed,
            'Execute Time' => $time . ' sec',
            'End Time' => date('Y-m-d H:i:s'),
        ]), 'info\\UserActivityController:actionGenerateRand:result');
    }
}
