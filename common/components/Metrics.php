<?php

namespace common\components;

use Prometheus\CollectorRegistry;
use Prometheus\Counter;
use Prometheus\Gauge;
use Prometheus\Histogram;
use sales\helpers\app\AppHelper;
use sales\helpers\setting\SettingHelper;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

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
    public const NAMESPACE_GLOBAL  = 'global';

    private array $counterList = [];
    private array $histogramList = [];
    private array $gaugeList = [];
    private CollectorRegistry $registry;
    private bool $isMetricsEnabled = false;
    private array $defaultBuckets = [0.2, 0.4, 0.6, 0.8, 1, 3, 5, 7, 10, 15];

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

    public function counterMetric(
        string $name,
        string $namespace,
        array $labels = [],
        string $prefix = '',
        int $value = 1
    ): void {
        if ($this->isMetricsEnabled) {
            $labels = self::prepareLabels($labels);
            try {
                $counter = $this->registry->getOrRegisterCounter(
                    self::stringToMetricStandard($namespace),
                    self::keyFormatter($name, 'cnt', $prefix),
                    self::helpFormatter($name, $prefix),
                    array_keys($labels)
                );
                $counter->incBy($value, array_values($labels));
            } catch (\Throwable $throwable) {
                \Yii::error(
                    ArrayHelper::merge(AppHelper::throwableLog($throwable), func_get_args()),
                    'Metrics:counterMetrics:Throwable'
                );
            }
        }
    }

    public function histogramMetric(
        string $name,
        float $value,
        array $labels,
        string $namespace = '',
        string $prefix = '',
        array $buckets = []
    ): void {
        if ($this->isMetricsEnabled) {
            $histogramBuckets = !empty($buckets) ? $buckets : $this->getDefaultBuckets();
            $labels = self::prepareLabels($labels);

            try {
                $histogram = $this->registry->getOrRegisterHistogram(
                    self::stringToMetricStandard($namespace),
                    self::keyFormatter($name, 'seconds', $prefix),
                    self::helpFormatter($name, $prefix),
                    array_keys($labels),
                    $histogramBuckets
                );
                $histogram->observe($value, array_values($labels));
            } catch (\Throwable $throwable) {
                \Yii::error(
                    ArrayHelper::merge(AppHelper::throwableLog($throwable), func_get_args()),
                    'Metrics:histogramMetric:Throwable'
                );
            }
        }
    }

    public function gaugeMetric(
        string $name,
        string $namespace,
        float $value,
        array $labels = [],
        string $prefix = ''
    ): void {
        if ($this->isMetricsEnabled) {
            $labels = self::prepareLabels($labels, true);
            try {
                $gauge = $this->registry->getOrRegisterGauge(
                    self::stringToMetricStandard($namespace),
                    self::keyFormatter($name, 'gauge', $prefix),
                    self::helpFormatter($name, $prefix),
                    array_keys($labels)
                );
                $gauge->set($value, array_values($labels));
            } catch (\Throwable $throwable) {
                \Yii::error(
                    ArrayHelper::merge(AppHelper::throwableLog($throwable), func_get_args()),
                    'Metrics:gaugeMetric:Throwable'
                );
            }
        }
    }

    public static function stringToMetricStandard(string $string, string $separator = '_'): string
    {
        return Inflector::slug(Inflector::camel2id($string, $separator), $separator);
    }

    public static function prepareLabels(array $labels, bool $keyOnly = false): array
    {
        $result = [];
        foreach ($labels as $key => $value) {
            if ($keyOnly) {
                $result[self::stringToMetricStandard($key)] = $value;
            } else {
                $result[self::stringToMetricStandard($key)] = self::stringToMetricStandard($value);
            }
        }
        return $result;
    }

    private static function keyFormatter(string $name, string $postfix, string $prefix = ''): string
    {
        return self::stringToMetricStandard($prefix . '_' . $name . '_' . $postfix);
    }

    private static function helpFormatter(string $name, string $prefix = ''): string
    {
        if ($prefix) {
            return $prefix . ' ' . strtolower($name);
        }
        return strtolower($name);
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

    public function getDefaultBuckets(): array
    {
        return $this->defaultBuckets;
    }
}
