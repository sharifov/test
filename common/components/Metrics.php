<?php

namespace common\components;

use Prometheus\CollectorRegistry;
use Prometheus\Counter;
use Prometheus\Gauge;
use Prometheus\Histogram;
use sales\helpers\app\AppHelper;
use sales\helpers\setting\SettingHelper;
use yii\base\Component;

/**
 * Class Metrics
 * @package common\components
 *
 * @property CollectorRegistry $registry
 * @property Counter[] $counterList
 * @property Histogram[] $histogramList
 * @property Gauge[] $gaugeList
 */
class Metrics extends Component
{
    public const NAMESPACE_CONSOLE  = 'console';
    public const NAMESPACE_FRONTEND = 'frontend';
    public const NAMESPACE_WEBAPI   = 'webapi';

    private array $counterList = [];
    private array $histogramList = [];
    private array $gaugeList = [];
    private CollectorRegistry $registry;
    private bool $isMetricsEnabled = false;

    public function init(): void
    {
        if ($this->isMetricsEnabled = SettingHelper::metricsEnabled()) {
            try {
                $this->registry = \Yii::$app->prometheus->registry;
            } catch (\Throwable $throwable) {
                \Yii::error(AppHelper::throwableLog($throwable), 'Metrics:init:Throwable');
            }
        }
    }

    /**
     * @param string $name
     * @param array $labels
     * @param int $value
     */
    public function jobCounter(string $name, array $labels = [], int $value = 1): void
    {
        if ($this->isMetricsEnabled) {
            $name = strtolower($name);
            $keyName = 'job_' . $name . '_cnt';
            try {
                $counter = $this->getCounterMetric($keyName);
                if (!$counter) {
                    $counter = $this->registry->registerCounter(
                        self::NAMESPACE_CONSOLE,
                        $keyName,
                        'Job ' . $name,
                        array_keys($labels)
                    );
                    $this->setCounterMetric($keyName, $counter);
                }
                $counter->incBy($value, array_values($labels));
            } catch (\Throwable $throwable) {
                \Yii::error(AppHelper::throwableLog($throwable), 'Metrics:jobCounter:Throwable');
            }
        }
    }

    /**
     * @param string $name
     * @param float $value
     * @param array $labels
     */
    public function jobGauge(string $name, float $value, array $labels = []): void
    {
        if ($this->isMetricsEnabled) {
            $name = strtolower($name);
            $keyName = 'job_' . $name;
            try {
                $gauge = $this->getGaugeMetric($keyName);
                if (!$gauge) {
                    $gauge = $this->registry->getOrRegisterGauge(
                        self::NAMESPACE_CONSOLE,
                        $keyName,
                        'Job ' . $name,
                        array_keys($labels)
                    );
                    $this->setGaugeMetric($keyName, $gauge);
                }
                $gauge->set($value, array_values($labels));
            } catch (\Throwable $throwable) {
                \Yii::error(AppHelper::throwableLog($throwable), 'Metrics:jobGauge:Throwable');
            }
        }
    }

    /**
     * @param string $name
     * @param float $value
     * @param array $labels
     */
    public function jobHistogram(string $name, float $value, array $labels = []): void
    {
        if ($this->isMetricsEnabled) {
            $name = strtolower($name);
            $keyName = 'job_' . $name;
            try {
                $histogram = $this->getHistogramMetric($keyName);
                if (!$histogram) {
                    $histogram = $this->registry->getOrRegisterHistogram(
                        self::NAMESPACE_CONSOLE,
                        $keyName,
                        'Job ' . $name,
                        array_keys($labels),
                        [0.4, 0.5, 0.7, 0.8, 0.9, 1, 3]
                    );
                    $this->setHistogramMetric($keyName, $histogram);
                }
                $histogram->observe($value, array_values($labels));
            } catch (\Throwable $throwable) {
                \Yii::error(AppHelper::throwableLog($throwable), 'Metrics:jobHistogram:Throwable');
            }
        }
    }

    /**
     * @param string $name
     * @param array $labels
     * @param int $value
     */
    public function serviceCounter(string $name, array $labels = [], int $value = 1): void
    {
        if ($this->isMetricsEnabled) {
            $name = strtolower($name);
            $keyName = 'service_' . $name . '_cnt';
            try {
                $counter = $this->getCounterMetric($keyName);
                if (!$counter) {
                    $counter = $this->registry->registerCounter(
                        self::NAMESPACE_CONSOLE,
                        $keyName,
                        'Service ' . $name,
                        array_keys($labels)
                    );
                    $this->setCounterMetric($keyName, $counter);
                }
                $counter->incBy($value, array_values($labels));
            } catch (\Throwable $throwable) {
                \Yii::error(AppHelper::throwableLog($throwable), 'Metrics:serviceCounter:Throwable');
            }
        }
    }

    /**
     * @param string $key
     * @return Counter|null
     */
    private function getCounterMetric(string $key = ''): ?Counter
    {
        return $this->counterList[$key] ?? null;
    }

    /**
     * @param string $key
     * @param Counter $counter
     */
    private function setCounterMetric(string $key, Counter $counter): void
    {
        $this->counterList[$key] = $counter;
    }

    /**
     * @param string $key
     * @return Histogram|null
     */
    private function getHistogramMetric(string $key = ''): ?Histogram
    {
        return $this->histogramList[$key] ?? null;
    }

    /**
     * @param string $key
     * @param Histogram $histogram
     */
    private function setHistogramMetric(string $key, Histogram $histogram): void
    {
        $this->histogramList[$key] = $histogram;
    }

    /**
     * @param string $key
     * @return Gauge|null
     */
    private function getGaugeMetric(string $key = ''): ?Gauge
    {
        return $this->gaugeList[$key] ?? null;
    }

    /**
     * @param string $key
     * @param Gauge $gauge
     */
    private function setGaugeMetric(string $key, Gauge $gauge): void
    {
        $this->gaugeList[$key] = $gauge;
    }
}
