<?php

namespace console\controllers;

use modules\abac\src\services\AbacDocService;
use sales\helpers\app\AppHelper;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use yii\helpers\VarDumper;

/**
 * Class AbacController
 */
class AbacController extends Controller
{
    public function actionScan(): int
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

        $timeStart = microtime(true);

        try {
            $abacDocService = new AbacDocService();
            $data = $abacDocService->parseFiles();
        } catch (\Throwable $throwable) {
            self::throwableHandler($throwable, 'actionApiLog:throwable:scan');
            return ExitCode::UNSPECIFIED_ERROR;
        }

        if ($data) {
            try {
                $abacDocService->insertData($data);
            } catch (\Throwable $throwable) {
                self::throwableHandler($throwable, 'actionApiLog:throwable:insert');
                return ExitCode::UNSPECIFIED_ERROR;
            }
        }

        $processed = count($data);
        $timeEnd = microtime(true);
        $time = number_format(round($timeEnd - $timeStart, 2), 2);
        self::outputResult($processed, $time, 'actionScan:result');
        return ExitCode::OK;
    }

    /**
     * @param \Throwable $throwable
     * @param string $category
     * @param string $categoryPrefix
     */
    private static function throwableHandler(
        \Throwable $throwable,
        string $category,
        string $categoryPrefix = 'AbacController:'
    ): void {
        Yii::error(
            AppHelper::throwableLog($throwable),
            $categoryPrefix . $category
        );
        echo Console::renderColoredString(
            '%r --- %RError: %n%r' . $throwable->getMessage()
        ), PHP_EOL;
    }

    /**
     * @param $processed
     * @param $time
     * @param string $category
     * @param string $categoryPrefix
     */
    private static function outputResult(
        $processed,
        $time,
        string $category,
        string $categoryPrefix = 'AbacController:'
    ): void {
        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time .
            ' s] %g Processed: %w[' . $processed . '] %g %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . ']%n'), PHP_EOL;

        Yii::info(VarDumper::dumpAsString([
            'Processed' => $processed,
            'Execute Time' => $time . ' sec',
            'End Time' => date('Y-m-d H:i:s'),
        ]), 'info\\' . $categoryPrefix . $category);
    }
}
