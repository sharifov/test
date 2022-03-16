<?php

namespace common\components\jobs;

use src\helpers\app\AppHelper;
use yii\queue\JobInterface;

/**
 * Class EventAppHandlerJob
 *
 * @property object $object
 * @property string $method
 * @property array|null $eventData
 * @property array|null $eventParams
 * @property array|null $handlerParams
 *
 * @property bool $enableLog
 * @property array $infoData
 */
class EventAppHandlerJob extends BaseJob implements JobInterface
{
    public object $object;
    public string $method;

    public ?array $eventData;
    public ?array $eventParams;
    public ?array $handlerParams;

    public bool $enableLog = false;
    public array $infoData = [];

    /**
     * @param object $object
     * @param string $method
     * @param array|null $eventData
     */
    public function __construct(object $object, string $method, ?array $eventData = null)
    {
        $this->object = $object;
        $this->method = $method;
        $this->eventData = $eventData;
        parent::__construct();
    }

    /**
     * @param $queue
     * @return void
     */
    public function execute($queue)
    {
        $this->waitingTimeRegister();
        try {
            $obj = $this->object;
            $method = $this->method;

            if ($this->enableLog) {
                $this->infoData['object'] = get_class($obj);
                $this->infoData['method'] = $this->method;
                \Yii::info($this->infoData, 'info\EventAppHandlerJob:execute-' . get_class($obj) . '::' . $method);
            }
            $obj->$method($this->eventData, $this->eventParams, $this->handlerParams);
        } catch (\Throwable $throwable) {
            $dataError = AppHelper::throwableLog($throwable);
            $dataError['info'] = $this->infoData;
            $dataError['job-data'] = [
                'object' => $this->object,
                'method' => $this->method,
                'eventData' => $this->eventData,
                'eventParams' => $this->eventParams,
                'handlerParams' => $this->handlerParams,
            ];

            \Yii::error($dataError, 'EventAppHandlerJob:execute');
        }
    }
}
