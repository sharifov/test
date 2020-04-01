<?php


namespace console\controllers;

use ReflectionClass;
use sales\helpers\app\AppHelper;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Class LogController
 * @package console\controllers
 * @property bool $logCleanerEnable
 * @property array $logCleanerParams
 * @property string $className
 * @property int $defaultDays
 * @property int $defaultLimit
 */
class LogController extends Controller
{
    public $defaultDays = 90;
    public $defaultLimit = 1000;

    private $logCleanerEnable;
    private $logCleanerParams;
    private $className;

    /**
     * LogController constructor.
     * @param $id
     * @param $module
     * @param array $config
     */
    public function __construct($id, $module, $config = [])
	{
		parent::__construct($id, $module, $config);
		$this->setSettings();
	}

	public function actionCleaner(?int $days, ?int $limit): void /* TODO::  arguments */
	{
	    if (!$this->logCleanerEnable) {
            $this->printInfo('Cleaner is disable. ', $this->action->id, Console::FG_RED);
	        return;
	    }

        $this->printInfo('Start. ',  $this->action->id);

        $days = $days ?? $this->logCleanerParams['days'];
        $limit = $limit ?? $this->logCleanerParams['limit'];

	    $timeStart = microtime(true);


        //$cleanLog = $this->cleanLog($days, $limit);


        $cleanGlobalLog = $this->cleanGlobalLog($days, $limit);

        $messageGlobalLog = 'GlobalLog clean result. Processed:' .
            $cleanGlobalLog['processed'] . ' ExecutionTime: ' . $cleanGlobalLog['executionTime'];

        $this->printInfo($messageGlobalLog,'cleanGlobalLog', $this->getColorInfo($cleanGlobalLog['status']));



        Yii::info($messageGlobalLog,'info\LogController:cleanGlobalLog');

        $status = ($cleanGlobalLog['status'] === 1); /* TODO:: add */
        $resultInfo = (!$status ? 'Are errors in the process. Please check error log' : 'End') .
            '. Total execution time: ' . number_format(round(microtime(true) - $timeStart, 2), 2);
        $this->printInfo($resultInfo, $this->action->id);
	}

	/**
     * @param int $days
     * @param int $limit
     * @return array [status,processed,executionTime]
     */
    private function cleanGlobalLog(int $days, int $limit): array
    {
        $prepareSql =
            'SELECT 
                MAX(gl_id) AS max_id,    
                COUNT(gl_id) AS cnt
            FROM
                global_log
            WHERE
                gl_created_at < SUBDATE(CURDATE(), :days)';

        $deleteSql = 'DELETE FROM global_log WHERE gl_id < :max_id limit :limit';

        return $this->baseCleaner($days, $limit, $prepareSql, $deleteSql, __FUNCTION__);
    }

    /**
     * @param int $days
     * @param int $limit
     * @return array [status,processed,executionTime]
     */
    private function cleanLog(int $days, int $limit): array
    {
        $prepareSql =
            'SELECT 
                MAX(id) AS max_id,    
                COUNT(id) AS cnt
            FROM
                log
            WHERE
                log_time < :days';

        $deleteSql = 'DELETE FROM log WHERE id < :max_id limit :limit';

        return $this->baseCleaner($days, $limit, $prepareSql, $deleteSql, __FUNCTION__);
    }

    /**
     * @param int $days
     * @param int $limit
     * @param string $prepareSql
     * @param string $deleteSql
     * @param string $methodName
     * @return array
     */
    private function baseCleaner(int $days, int $limit, string $prepareSql, string $deleteSql, string $methodName): array
    {
        $processed = 0;
        $timeStart = microtime(true);

        $result = [
            'status' => 1,
            'processed' => $processed,
            'executionTime' => $timeStart,
        ];

        try {
            $prepareInfo = Yii::$app->db->createCommand($prepareSql)
                ->bindValue(':days', $days)
                ->queryOne();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableFormatter($throwable),
            $this->className . ':' . $methodName . ':FailedPrepareInfo');
            $result['status'] = -1;

            return $result;
        }

        $iterations =  (int)($prepareInfo['cnt'] / $limit);

        for ($i = 0; $i <= $iterations; $i++) {
            try {
                $processed += Yii::$app->db->createCommand($deleteSql)
                            ->bindValues([':max_id' => $prepareInfo['max_id'], ':limit' => $limit])
                            ->execute();
            } catch (\Throwable $throwable) {
                $result['status'] = 0;
                Yii::error(AppHelper::throwableFormatter($throwable),
            $this->className. ':' . $methodName . ':FailedDelete');
            }
        }

        $result['processed'] = $processed;
        $result['executionTime'] = number_format(round(microtime(true) - $timeStart, 2), 2);

        return $result;
    }

	private function cleanLogOld(int $days, int $limit): array
    {
        $processed = 0;
        $timeStart = microtime(true);

        $result = [
            'status' => 1,
            'processed' => $processed,
            'executionTime' => $timeStart,
        ];

        try {
            $prepareInfo = Yii::$app->db->createCommand(
            'SELECT 
                    MAX(id) AS max_id,    
                    COUNT(id) AS cnt
                FROM
                    log
                WHERE
                    log_time < :days'
            )->bindValue(':days', strtotime('-' . $days . ' day'))->queryOne();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableFormatter($throwable),
            $this->className . ':' . __FUNCTION__ . ':FailedPrepareInfo');

            $result['status'] = -1;
            return $result;
        }

        $iterations = (int)($prepareInfo['cnt'] / $limit);

        for ($i = 0; $i <= $iterations; $i++) {
            try {
                $processed += Yii::$app->db->createCommand(
                            'DELETE FROM log WHERE id < :max_id limit :limit')
                            ->bindValues([':max_id' => $prepareInfo['max_id'], ':limit' => $limit])
                            ->execute();
            } catch (\Throwable $throwable) {
                $result['status'] = 0;
                Yii::error(AppHelper::throwableFormatter($throwable),
            $this->className. ':' . __FUNCTION__ . ':FailedDelete');
            }
        }

        $result['processed'] = $processed;
        $result['executionTime'] = number_format(round(microtime(true) - $timeStart, 2), 2);

        return $result;
    }

    /**
     * @param int $days
     * @param int $limit
     * @return array [status,processed,executionTime]
     */
    private function cleanGlobalLogOld(int $days, int $limit): array
    {
        $processed = 0;
        $timeStart = microtime(true);

        $result = [
            'status' => 1,
            'processed' => $processed,
            'executionTime' => $timeStart,
        ];

        try {
            $prepareInfo = Yii::$app->db->createCommand(
            'SELECT 
                    MAX(gl_id) AS max_id,    
                    COUNT(gl_id) AS cnt
                FROM
                    global_log
                WHERE
                    gl_created_at < SUBDATE(CURDATE(), :days)'
            )->bindValue(':days', $days)->queryOne();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableFormatter($throwable),
            $this->className . ':' . __FUNCTION__ . ':FailedPrepareInfo');

            $result['status'] = -1;
            return $result;
        }

        $iterations =  (int)($prepareInfo['cnt'] / $limit);

        for ($i = 0; $i <= $iterations; $i++) {
            try {
                $processed += Yii::$app->db->createCommand(
                            'DELETE FROM global_log WHERE gl_id < :max_id limit :limit')
                            ->bindValues([':max_id' => $prepareInfo['max_id'], ':limit' => $limit])
                            ->execute();
            } catch (\Throwable $throwable) {
                $result['status'] = 0;
                Yii::error(AppHelper::throwableFormatter($throwable),
            $this->className. ':' . __FUNCTION__ . ':FailedDelete');
            }
        }

        $result['processed'] = $processed;
        $result['executionTime'] = number_format(round(microtime(true) - $timeStart, 2), 2);

        return $result;
    }

    /**
     * @return LogController
     */
    private function setSettings(): LogController
    {
        try {
            $this->className = (new ReflectionClass(self::class))->getShortName();
        } catch (\Throwable $throwable) {
            $this->className = self::class;
        }

        $settings = Yii::$app->params['settings'];
		$this->logCleanerEnable = $settings['console_log_cleaner_enable'] ?? false;

		try {
		    $logCleanerParams = json_decode($settings['console_log_cleaner_params'],true,512,JSON_THROW_ON_ERROR);
		    $this->logCleanerParams = [
		        'days' => $logCleanerParams['days'],
		        'limit' => $logCleanerParams['limit'],
            ];
		} catch (\Throwable $throwable) {
		   $this->logCleanerParams = [
		        'days' => $this->defaultDays,
		        'limit' => $this->defaultLimit,
           ];
           Yii::error(AppHelper::throwableFormatter($throwable),
            $this->className. ':' . __FUNCTION__ . ':FailedJsonDecode');
		}

        return $this;
    }

    /**
     * @param int $statusCode
     * @return int
     */
    private function getColorInfo(int $statusCode): int
    {
         switch ($statusCode) {
            case -1:
                $colorInfo = Console::FG_RED;
                break;
            case 0:
                $colorInfo = Console::FG_YELLOW;
                break;
            default:
                $colorInfo = Console::FG_GREEN;
        }

        return $colorInfo;
    }

    /**
     * @param string $info
     * @param string $function
     * @param int $colorInfo
     * @param int $color
     */
    private function printInfo(string $info, string $function = '', $colorInfo = Console::FG_GREEN, $color = Console::FG_CYAN): void
    {
        printf(
            "\n --- %s %s --- \n",
            $this->ansiFormat($info, $colorInfo),
            $this->ansiFormat('(' . $this->className . ':' . $function . ')', $color)
        );
    }

}