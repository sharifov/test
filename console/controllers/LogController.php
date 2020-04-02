<?php


namespace console\controllers;

use common\models\ApiLog;
use common\models\GlobalLog;
use frontend\models\Log;
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
 * @property array $cleanerCollection
 */
class LogController extends Controller
{
    public $defaultDays = 90;
    public $defaultLimit = 1000;

    private $logCleanerEnable;
    private $logCleanerParams;
    private $className;
    private $cleanerCollection;

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

    /**
     * @param int|null $days
     * @param int|null $limit
     */
    public function actionCleaner(?int $days = null, ?int $limit = null): void
	{
	    if (!$this->logCleanerEnable) {
            $this->printInfo('Cleaner is disable. ', $this->action->id, Console::FG_RED);
	        return;
	    }

        $this->printInfo('Start. ',  $this->action->id);

        $days = $days ?? $this->logCleanerParams['days'];
        $limit = $limit ?? $this->logCleanerParams['limit'];

        $this->printInfo('Days:' . $days,  $this->action->id);

	    $timeStart = microtime(true);

        foreach ($this->cleanerCollection as $table => $params) {

            $result = $this->baseCleaner($days, $limit, $params['prepareSql'], $params['deleteSql'], $table);

            $message = '"' . $table . '" - Processed:' .
                $result['processed'] . ' ExecutionTime: ' . $result['executionTime'];
            if ($result['status'] !== 1) {
                $message .= ' Process are errors, check error logs';
            }

            $this->printInfo($message, 'cleaner:' . $table, $this->getColorInfo($result['status']));
            Yii::info($message,'info\LogController:cleaner:' . $table);
        }

        $resultInfo = 'End. Total execution time: ' . number_format(round(microtime(true) - $timeStart, 2), 2);
        $this->printInfo($resultInfo, $this->action->id);
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
            $result['executionTime'] = number_format(round(microtime(true) - $timeStart, 2), 2);
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
		    $this->logCleanerParams = [
		        'days' => $settings['console_log_cleaner_params']['days'],
		        'limit' => $settings['console_log_cleaner_params']['limit'],
            ];
		} catch (\Throwable $throwable) {
		   $this->logCleanerParams = [
		        'days' => $this->defaultDays,
		        'limit' => $this->defaultLimit,
           ];
           Yii::error(AppHelper::throwableFormatter($throwable),
            $this->className. ':' . __FUNCTION__ . ':FailedJsonDecode');
		}

        $this->cleanerCollection[GlobalLog::tableName()] =
            [
                'prepareSql' => 'SELECT MAX(gl_id) AS max_id, COUNT(gl_id) AS cnt 
                    FROM ' . GlobalLog::tableName() . ' WHERE gl_created_at < SUBDATE(CURDATE(), :days)',
                'deleteSql' => 'DELETE FROM ' . GlobalLog::tableName() . ' WHERE gl_id < :max_id limit :limit'
            ];
        $this->cleanerCollection[Log::tableName()] =
            [
                'prepareSql' => 'SELECT MAX(id) AS max_id, COUNT(id) AS cnt 
                    FROM ' . Log::tableName() . ' WHERE log_time < UNIX_TIMESTAMP(SUBDATE(CURDATE(), :days))',
                'deleteSql' => 'DELETE FROM ' . Log::tableName() . ' WHERE id < :max_id limit :limit'
            ];
        $this->cleanerCollection[ApiLog::tableName()] =
            [
                'prepareSql' => 'SELECT MAX(al_id) AS max_id, COUNT(al_id) AS cnt 
                    FROM ' . ApiLog::tableName() . ' WHERE al_request_dt < SUBDATE(CURDATE(), :days)',
                'deleteSql' => 'DELETE FROM ' . ApiLog::tableName() . ' WHERE al_id < :max_id limit :limit'
            ];

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