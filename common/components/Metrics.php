<?php
namespace common\components;

use Prometheus\CollectorRegistry;
use sales\helpers\app\AppHelper;
use yii\base\Component;

/**
 * Class Metrics
 * @package common\components
 *
 * @property CollectorRegistry $_registry
 */
class Metrics extends Component
{
    public const NAMESPACE_CONSOLE  = 'console';
    public const NAMESPACE_FRONTEND = 'frontend';
    public const NAMESPACE_WEBAPI   = 'webapi';

    private CollectorRegistry $_registry;

    public function init() : void
    {
        // parent::init();
        try {
            $this->_registry = \Yii::$app->prometheus->registry;
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'Metrics:init:Throwable');
        }
    }

    /**
     * @param string $name
     * @param int $value
     */
    public function jobCounter(string $name, int $value = 1): void
    {
        try {
            $counter = $this->_registry->registerCounter(self::NAMESPACE_CONSOLE, 'queue_jobs', 'Console queue Jobs Counter', ['name']);
            $counter->incBy($value, [$name]);
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'Metrics:jobCounter:Throwable');
        }
    }

    /**
     * @param string $name
     * @param float $value
     */
    public function jobGauge(string $name, float $value): void
    {
        try {
            $gauge = $this->_registry->getOrRegisterGauge(self::NAMESPACE_CONSOLE, 'queue_jobs', 'Console queue Jobs Gauge', ['name']);
            $gauge->set($value, [$name]);
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'Metrics:jobGauge:Throwable');
        }
    }

    /**
     * @param string $name
     * @param float $value
     * @param array $labels
     */
    public function jobHistogram(string $name, float $value, array $labels = []): void
    {
        $name = strtolower($name);
        try {
            $histogram = $this->_registry->getOrRegisterHistogram(
                self::NAMESPACE_CONSOLE,
                'job_' . $name,
                'Job ' . $name,
                array_keys($labels),
                [0.1, 0.3, 0.5, 0.8, 1, 2, 3, 5, 7, 10]
            );
            $histogram->observe($value, array_values($labels));
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'Metrics:jobHistogram:Throwable');
        }
    }


    /**
     * @param string $name
     * @param array $labels
     * @param int $value
     */
    public function serviceCounter(string $name, array $labels = [], int $value = 1): void
    {
        $name = strtolower($name);
        try {
            $counter = $this->_registry->registerCounter(self::NAMESPACE_CONSOLE, 'service_' . $name . '_cnt', 'Service ' . $name, array_keys($labels));
            $counter->incBy($value, array_values($labels));
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'Metrics:serviceCounter:Throwable');
        }
    }
}
