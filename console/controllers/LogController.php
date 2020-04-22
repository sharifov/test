<?php


namespace console\controllers;

use common\models\ApiLog;
use common\models\GlobalLog;
use console\helpers\OutputHelper;
use frontend\models\Log;
use sales\helpers\app\AppHelper;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Class LogController
 * @property bool $logCleanerEnable
 * @property array $logCleanerParams
 * @property string $shortClassName
 * @property int $defaultDays
 * @property int $defaultLimit
 * @property array $cleanerCollection
 * @property OutputHelper $outputHelper
 */
class LogController extends Controller
{
    public $defaultDays = 90;
    public $defaultLimit = 1000;

    private $logCleanerEnable = false;
    private $logCleanerParams;
    private $shortClassName;
    private $cleanerCollection;
    private $outputHelper;

    /**
     * @param $id
     * @param $module
     * @param OutputHelper $outputHelper
     * @param array $config
     */
    public function __construct($id, $module, OutputHelper $outputHelper, $config = [])
	{
		parent::__construct($id, $module, $config);
		$this->outputHelper = $outputHelper;
		$this->setSettings();
	}

    /**
     * @param int|null $days
     * @param int|null $limit
     */
    public function actionCleaner(?int $days = null, ?int $limit = null): void
	{
	    $infoMessage = '';
	    $timeStart = microtime(true);
	    $days = $days ?? $this->logCleanerParams['days'];
	    $limit = $limit ?? $this->logCleanerParams['limit'];
	    $point = $this->shortClassName . ':' .$this->action->id;
	    
	    if (!$this->logCleanerEnable) {
            $this->outputHelper->printInfo('Cleaner is disable. ', $point, Console::FG_RED);
	        return;
	    }

        $this->outputHelper->printInfo('Start. ', $point);

        foreach ($this->cleanerCollection as $table => $params) {

            $result = $this->baseCleaner($days, $limit, $params['prepareSql'], $params['deleteSql'], $table);

            $message = '"' . $table . '" - Processed: ' . $result['processed'] . ' ExecutionTime: ' . $result['executionTime'];
            $message .= $result['status'] !== 1 ? ' Process are errors, check error logs' : '';

            $this->outputHelper->printInfo($message, $point . ':' . $table, OutputHelper::getColorByStatusCode($result['status']));
            $infoMessage .= $message . "\n";
        }

        $resultInfo = 'Total execution time: ' . number_format(round(microtime(true) - $timeStart, 2), 2);
        $this->outputHelper->printInfo($resultInfo, $point);
        Yii::info($infoMessage . $resultInfo,'info\LogController:done');
	}

    /**
     * @param int $days
     * @param int $limit
     * @param string $prepareSql
     * @param string $deleteSql
     * @param string $tableName
     * @return array
     */
    private function baseCleaner(int $days, int $limit, string $prepareSql, string $deleteSql, string $tableName): array
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
            $this->shortClassName . ':' . $tableName . ':FailedPrepareInfo');
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
            $this->shortClassName. ':' . $tableName . ':FailedDelete');
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
        $this->shortClassName = OutputHelper::getShortClassName(self::class);

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
           Yii::error(AppHelper::throwableFormatter($throwable),$this->shortClassName. ':' . __FUNCTION__ . ':Failed');
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
}