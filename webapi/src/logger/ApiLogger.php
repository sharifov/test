<?php

namespace webapi\src\logger;

use Yii;
use common\models\ApiLog;

/**
 * Class ApiLogger
 *
 * @property ApiLog $log;
 * @property TechnicalData[] $technicalData;
 */
class ApiLogger
{
    public $log;

    private $technicalData = [];

    public function start(StartDTO $dto): void
    {
        $log = ApiLog::start($dto);

        if (!$log->save()) {
            Yii::error(print_r($log->errors, true), 'ApiLogger:start:save');
            return;
        }
        $this->log = $log;
    }

    public function end(EndDTO $dto): void
    {
        if (($log = $this->log) === null) {
            Yii::error('Cant end Apilog, log is null', 'ApiLogger:end');
            return;
        }

        $log->end($dto);

        if (!$log->save()) {
            Yii::error(print_r($log->errors, true), 'ApiLog:end:save');
        }
    }

    public function getTechnicalInfo(): array
    {
        if (($log = $this->log) === null) {
            Yii::error('Cant getTechnicalInfo Apilog, log is null', 'ApiLogger:getTechnicalInfo');
            return [];
        }

        return array_merge([
            'action' => $log->al_action,
            'response_id' => $log->al_id,
            'request_dt' => $log->al_request_dt,
            'response_dt' => $log->al_response_dt,
            'execution_time' => $log->al_execution_time,
            'memory_usage' => $log->al_memory_usage,
        ], $this->technicalData);
    }

    public function addTechnicalData(TechnicalData $data): void
    {
        $this->technicalData[$data->getKey()] = $data->getData();
    }
}
