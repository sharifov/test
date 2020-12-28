<?php

namespace sales\behaviors\metric;

use common\components\Metrics;
use ReflectionClass;
use sales\helpers\app\AppHelper;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class MetricObjectCounterBehavior
 *
 * @property array $labels
 * @property string $namespace
 * @property string $prefix
 */
class MetricObjectCounterBehavior extends Behavior
{
    public string $name;
    public string $namespace = '';
    public array $labels = ['action' => 'created'];
    public string $prefix = 'Object';
    public int $value = 1;

    /**
     * @return array
     */
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'addMetricCount',
        ];
    }

    public function addMetricCount(): void
    {
        $this->fillDefaultValue();
        $this->fillCustomValue();

        try {
            if (empty($this->name)) {
                throw new \Exception('Name is required');
            }

            $metrics = \Yii::$container->get(Metrics::class);
            $metrics->counterMetric(
                $this->name,
                $this->namespace,
                $this->labels,
                $this->prefix,
                $this->value
            );
        } catch (\Throwable $throwable) {
            \Yii::error(
                ArrayHelper::merge(AppHelper::throwableLog($throwable), get_object_vars($this)),
                'MetricObjectCounterBehavior:addMetricCount:Throwable'
            );
        }
    }

    private function fillDefaultValue(): void
    {
        try {
            if (empty($this->name)) {
                $this->name = (new ReflectionClass($this->owner))->getShortName();
            }
            if (property_exists($this->owner, 'metricNamespace') && $this->owner->metricNamespace) {
                $this->namespace = $this->owner->metricNamespace;
            }
            if (property_exists($this->owner, 'metricLabels') && $this->owner->metricLabels) {
                $this->labels = $this->owner->metricLabels;
            }
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'MetricObjectCounterBehavior:fillDefaultValue:Throwable'
            );
        }
    }

    public function fillCustomValue(): void
    {
    }
}
