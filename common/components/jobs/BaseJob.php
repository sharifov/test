<?php

namespace common\components\jobs;

use common\components\Metrics;
use console\helpers\OutputHelper;
use src\helpers\app\AppHelper;
use src\helpers\setting\SettingHelper;
use yii\base\BaseObject;

/**
 * Class BaseJob
 *
 * @property float $timeStart
 * @property int $delayJob
 */
class BaseJob extends BaseObject
{
    public float $timeStart;
    public int $delayJob = 0;

    private array $defaultBuckets = [1, 3, 5, 7, 10, 15, 30, 60, 300];

    /**
     * @param float|null $timeStart
     * @param array $config
     */
    public function __construct(?float $timeStart = null, $config = [])
    {
        $this->timeStart = $timeStart ?? microtime(true);
        parent::__construct($config);
    }

    public function waitingTimeRegister(?array $buckets = null): bool
    {
        if (!$limitWaiting = SettingHelper::getMetricJobTimeWaiting()) {
            return false;
        }

        try {
            $metrics = \Yii::$container->get(Metrics::class);
            $seconds = round(microtime(true) - $this->timeStart, 1);
            $seconds -= $this->delayJob;
            $buckets = empty($buckets) ? $this->defaultBuckets : $buckets;
            $metrics->histogramMetric(
                'job_execute',
                $seconds,
                ['jobName' => self::runInClass()],
                Metrics::NAMESPACE_CONSOLE,
                '',
                $buckets
            );

            if ($seconds > $limitWaiting) {
                \Yii::warning(
                    'Warning: (' . self::runInClass() . ') timeout exceeded. Time (' . $seconds . ') sec',
                    'BaseJob:WaitingTimeRegister:TimeoutExceeded'
                );
            }
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'BaseJob:WaitingTimeRegister:Throwable');
            return false;
        }
        return true;
    }

    public static function runInClass(): string
    {
        return OutputHelper::getShortClassName(static::class);
    }
}
