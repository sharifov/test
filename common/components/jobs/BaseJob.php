<?php

namespace common\components\jobs;

use common\components\Metrics;
use console\helpers\OutputHelper;
use sales\helpers\app\AppHelper;
use sales\helpers\setting\SettingHelper;
use yii\base\BaseObject;

/**
 * Class BaseJob
 */
class BaseJob extends BaseObject
{
    public float $timeStart;
    public Metrics $metrics;

    private array $defaultBuckets = [1, 3, 5, 7, 10, 15, 30, 45, 60, 90];

    /**
     * @param Metrics $metrics
     * @param array $config
     */
    public function __construct(?Metrics $metrics = null, $config = [])
    {
        $this->timeStart = microtime(true);
        $this->metrics = $metrics ?? \Yii::$container->get(Metrics::class);
        parent::__construct($config);
    }

    public function executionTimeRegister(array $buckets = null): bool
    {
        if (!$limitExecution = SettingHelper::getMetricJobTimeExecution()) {
            return false;
        }

        try {
            $seconds = round(microtime(true) - $this->timeStart, 1);
            $buckets = empty($buckets) ? $this->defaultBuckets : $buckets;
            $this->metrics->histogramMetric(
                'job_execute',
                $seconds,
                ['jobName' => self::runInClass()],
                Metrics::NAMESPACE_CONSOLE,
                '',
                $buckets
            );

            if ($seconds > $limitExecution) {
                \Yii::warning(
                    'Warning: (' . self::runInClass() . ') exceeded execution time limit. Execution time (' . $seconds . ') sec',
                    'BaseJob:executionTimeRegister:TimeLimitExceeded'
                );
            }
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'BaseJob:executionTimeRegister:Throwable');
            return false;
        }
        return true;
    }

    public static function runInClass(): string
    {
        return OutputHelper::getShortClassName(static::class);
    }
}
