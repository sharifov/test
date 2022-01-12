<?php

namespace console\controllers;

use src\model\voip\phoneDevice\log\PhoneDeviceLog;
use yii\console\Controller;
use yii\helpers\Console;

class VoipController extends Controller
{
    public function actionCleanPhoneDeviceLog()
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;
        $timeStart = microtime(true);

        PhoneDeviceLog::deleteAll(['<', 'pdl_timestamp_dt', (new \DateTimeImmutable())->modify('-30 days')->format('Y-m-d 00:00:00')]);

        $timeEnd = microtime(true);
        $time = number_format(round($timeEnd - $timeStart, 2), 2);
        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time .
            ' s] %g'), PHP_EOL;
        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;
    }
}
