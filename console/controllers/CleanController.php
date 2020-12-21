<?php

namespace console\controllers;

use sales\helpers\app\AppHelper;
use sales\helpers\ErrorsToStringHelper;
use sales\helpers\setting\SettingHelper;
use sales\services\cleaner\cleaners\ApiLogCleaner;
use sales\services\cleaner\cleaners\CallCleaner;
use sales\services\cleaner\cleaners\GlobalLogCleaner;
use sales\services\cleaner\cleaners\LogCleaner;
use sales\services\cleaner\cleaners\UserMonitorCleaner;
use sales\services\cleaner\cleaners\UserSiteActivityCleaner;
use sales\services\cleaner\DbCleanerService;
use sales\services\cleaner\form\DbCleanerParamsForm;
use Yii;
use yii\base\Exception;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use yii\helpers\VarDumper;

/**
 * Class CleanController
 */
class CleanController extends Controller
{
    public $hour;
    public $day;
    public $month;
    public $year;
    public $date;
    public $datetime;
    public $strict_date;

    public function options($actionID): array
    {
        return array_merge(
            parent::options($actionID),
            DbCleanerService::ALLOWED_PARAMS
        );
    }

    public function optionAliases(): array
    {
        return [
            'h' => 'hour',
            'd' => 'day',
            'm' => 'month',
            'y' => 'year',
        ];
    }

    public function actionOnceDay(): int
    {
        echo Console::renderColoredString('%y --- Start %w[' . date('Y-m-d H:i:s') . '] %y' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

        $timeStart = microtime(true);

        try {
            $paramsLog = [
                'date' => DbCleanerService::daysToBeforeDate(SettingHelper::consoleLogCleanerParamsDays())
            ];
            \Yii::$app->runAction('clean/log', $paramsLog);
        } catch (\Throwable $throwable) {
            self::throwableHandler($throwable, 'actionOnceDay:Log');
        }
        try {
            $paramsApiLog = [
                'date' => DbCleanerService::daysToBeforeDate(SettingHelper::consoleLogCleanerParamsDays())
            ];
            \Yii::$app->runAction('clean/api-log', $paramsApiLog);
        } catch (\Throwable $throwable) {
            self::throwableHandler($throwable, 'actionOnceDay:ApiLog');
        }
        try {
            $paramsGlobalLog = [
                'date' => DbCleanerService::daysToBeforeDate(SettingHelper::consoleLogCleanerParamsDays())
             ];
            \Yii::$app->runAction('clean/global-log', $paramsGlobalLog);
        } catch (\Throwable $throwable) {
            self::throwableHandler($throwable, 'actionOnceDay:GlobalLog');
        }
        try {
            $paramsCall = [
                'date' => DbCleanerService::daysToBeforeDate(SettingHelper::cleanCallAfterDays())
            ];
            \Yii::$app->runAction('clean/call', $paramsCall);
        } catch (\Throwable $throwable) {
            self::throwableHandler($throwable, 'actionOnceDay:Call');
        }
        try {
            $paramsUserSiteActivity = [
                'date' => DbCleanerService::daysToBeforeDate(SettingHelper::userSiteActivityLogHistoryDays())
            ];
            \Yii::$app->runAction('clean/user-site-activity', $paramsUserSiteActivity);
        } catch (\Throwable $throwable) {
            self::throwableHandler($throwable, 'actionOnceDay:UserSiteActivity');
        }
        try {
            $paramsUserMonitor = [
                'date' => DbCleanerService::daysToBeforeDate(SettingHelper::cleanUserMonitorAfterDays())
            ];
            \Yii::$app->runAction('clean/user-monitor', $paramsUserMonitor);
        } catch (\Throwable $throwable) {
            self::throwableHandler($throwable, 'actionOnceDay:UserMonitor');
        }

        $timeEnd = microtime(true);
        $time = number_format(round($timeEnd - $timeStart, 2), 2);
        echo Console::renderColoredString('%y --- Execute Time: %w[' . $time . ' s] %n'), PHP_EOL;
        echo Console::renderColoredString('%y --- End : %w[' . date('Y-m-d H:i:s') . '] %y' .
            self::class . ':' . __FUNCTION__ . '%n'), PHP_EOL;
        return ExitCode::OK;
    }

    public function actionApiLog(): int
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

        $timeStart = microtime(true);
        $params = $this->mappingParams();
        $cleaner = new ApiLogCleaner();
        $defaultDays = SettingHelper::consoleLogCleanerParamsDays();

        try {
            if (!SettingHelper::consoleLogCleanerEnable()) {
                throw new Exception('Setting (console_log_cleaner_enable) is disable. Script stopped.');
            }

            $dbCleanerParamsForm = (new DbCleanerParamsForm())
                ->setTable($cleaner->getTable())
                ->setColumn($cleaner->getColumn())
                ->fillParam($params);

            if ($defaultDays && self::isParamsEmpty($params)) {
                $dbCleanerParamsForm->day = $defaultDays;
            }
            if (!$dbCleanerParamsForm->validate()) {
                throw new Exception(ErrorsToStringHelper::extractFromModel($dbCleanerParamsForm));
            }
            $processed = $cleaner->runDeleteByForm($dbCleanerParamsForm);
        } catch (\Throwable $throwable) {
            self::throwableHandler($throwable, 'actionApiLog:throwable');
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $timeEnd = microtime(true);
        $time = number_format(round($timeEnd - $timeStart, 2), 2);
        self::outputResult($processed, $time, 'actionApiLog:result');
        return ExitCode::OK;
    }

    public function actionLog(): int
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

        $timeStart = microtime(true);
        $params = $this->mappingParams();
        $cleaner = new LogCleaner();
        $defaultDays = SettingHelper::consoleLogCleanerParamsDays();

        try {
            if (!SettingHelper::consoleLogCleanerEnable()) {
                throw new Exception('Setting (console_log_cleaner_enable) is disable. Script stopped.');
            }

            $dbCleanerParamsForm = (new DbCleanerParamsForm())
                ->setTable($cleaner->getTable())
                ->setColumn($cleaner->getColumn())
                ->fillParam($params);

            if ($defaultDays && self::isParamsEmpty($params)) {
                $dbCleanerParamsForm->day = $defaultDays;
            }
            if (!$dbCleanerParamsForm->validate()) {
                throw new Exception(ErrorsToStringHelper::extractFromModel($dbCleanerParamsForm));
            }

            $processed = $cleaner->runDeleteByForm($dbCleanerParamsForm);
        } catch (\Throwable $throwable) {
            self::throwableHandler($throwable, 'actionLog:throwable');
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $timeEnd = microtime(true);
        $time = number_format(round($timeEnd - $timeStart, 2), 2);
        self::outputResult($processed, $time, 'actionLog:result');
        return ExitCode::OK;
    }

    public function actionGlobalLog(): int
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

        $timeStart = microtime(true);
        $params = $this->mappingParams();
        $cleaner = new GlobalLogCleaner();
        $defaultDays = SettingHelper::consoleLogCleanerParamsDays();

        try {
            if (!SettingHelper::consoleLogCleanerEnable()) {
                throw new Exception('Setting (console_log_cleaner_enable) is disable. Script stopped.');
            }

            $dbCleanerParamsForm = (new DbCleanerParamsForm())
                ->setTable($cleaner->getTable())
                ->setColumn($cleaner->getColumn())
                ->fillParam($params);

            if ($defaultDays && self::isParamsEmpty($params)) {
                $dbCleanerParamsForm->day = $defaultDays;
            }

            if (!$dbCleanerParamsForm->validate()) {
                throw new Exception(ErrorsToStringHelper::extractFromModel($dbCleanerParamsForm));
            }

            $processed = $cleaner->runDeleteByForm($dbCleanerParamsForm);
        } catch (\Throwable $throwable) {
            self::throwableHandler($throwable, 'actionGlobalLog:throwable');
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $timeEnd = microtime(true);
        $time = number_format(round($timeEnd - $timeStart, 2), 2);
        self::outputResult($processed, $time, 'actionGlobalLog:result');
        return ExitCode::OK;
    }

    public function actionCall(): int
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

        $timeStart = microtime(true);
        $params = $this->mappingParams();
        $cleaner = new CallCleaner();

        try {
            $dbCleanerParamsForm = (new DbCleanerParamsForm())
                ->setTable($cleaner->getTable())
                ->setColumn($cleaner->getColumn())
                ->fillParam($params);

            if (!$dbCleanerParamsForm->validate()) {
                throw new Exception(ErrorsToStringHelper::extractFromModel($dbCleanerParamsForm));
            }

            $processed = $cleaner->runDeleteByForm($dbCleanerParamsForm);
        } catch (\Throwable $throwable) {
            self::throwableHandler($throwable, 'actionCall:throwable');
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $timeEnd = microtime(true);
        $time = number_format(round($timeEnd - $timeStart, 2), 2);
        self::outputResult($processed, $time, 'actionCall:result');
        return ExitCode::OK;
    }

    public function actionUserSiteActivity(): int
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

        $timeStart = microtime(true);
        $params = $this->mappingParams();
        $cleaner = new UserSiteActivityCleaner();
        $defaultDays = SettingHelper::userSiteActivityLogHistoryDays();

        try {
            $dbCleanerParamsForm = (new DbCleanerParamsForm())
                ->setTable($cleaner->getTable())
                ->setColumn($cleaner->getColumn())
                ->fillParam($params);

            if ($defaultDays && self::isParamsEmpty($params)) {
                $dbCleanerParamsForm->day = $defaultDays;
            }
            if (!$dbCleanerParamsForm->validate()) {
                throw new Exception(ErrorsToStringHelper::extractFromModel($dbCleanerParamsForm));
            }

            $processed = $cleaner->runDeleteByForm($dbCleanerParamsForm);
        } catch (\Throwable $throwable) {
            self::throwableHandler($throwable, 'actionUserSiteActivity:throwable');
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $timeEnd = microtime(true);
        $time = number_format(round($timeEnd - $timeStart, 2), 2);
        self::outputResult($processed, $time, 'actionUserSiteActivity:result');

        return ExitCode::OK;
    }

    public function actionUserMonitor(): int
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

        $timeStart = microtime(true);
        $params = $this->mappingParams();
        $userMonitorCleaner = new UserMonitorCleaner();

        try {
            $dbCleanerParamsForm = (new DbCleanerParamsForm())
                ->setTable($userMonitorCleaner->getTable())
                ->setColumn($userMonitorCleaner->getColumn())
                ->fillParam($params);

            if (!$dbCleanerParamsForm->validate()) {
                throw new Exception(ErrorsToStringHelper::extractFromModel($dbCleanerParamsForm));
            }

            $processed = $userMonitorCleaner->runDeleteByForm($dbCleanerParamsForm);
        } catch (\Throwable $throwable) {
            self::throwableHandler($throwable, 'actionUserMonitor:throwable');
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $timeEnd = microtime(true);
        $time = number_format(round($timeEnd - $timeStart, 2), 2);
        self::outputResult($processed, $time, 'actionUserMonitor:result');
        return ExitCode::OK;
    }

    private function mappingParams(): array
    {
        return [
            'hour' => $this->hour,
            'day' => $this->day,
            'month' => $this->month,
            'year' => $this->year,
            'date' => $this->date,
            'datetime' => $this->datetime,
            'strict_date' => $this->strict_date,
        ];
    }

    private static function isParamsEmpty(array $params): bool
    {
        foreach ($params as $value) {
            if (!empty($value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param \Throwable $throwable
     * @param string $category
     * @param string $categoryPrefix
     */
    private static function throwableHandler(
        \Throwable $throwable,
        string $category,
        string $categoryPrefix = 'CleanController:'
    ): void {
        Yii::error(
            AppHelper::throwableFormatter($throwable),
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
        string $categoryPrefix = 'CleanController:'
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
