<?php

namespace sales\traits;

use common\components\Metrics;

/**
 * Trait MetricObjectTrait
 *
 * @property array|null $metricLabels
 * @property string|null $metricNamespace
 */
trait MetricObjectCounterTrait
{
    public $metricLabels;
    public $metricNamespace;

    /**
     * @param array $metricLabels
     * @return $this
     */
    public function setMetricLabels(array $metricLabels): self
    {
        $this->metricLabels = $metricLabels;
        return $this;
    }

    /**
     * @param string $metricNamespace
     * @return $this
     */
    public function setMetricNamespace(string $metricNamespace): self
    {
        $this->metricNamespace = $metricNamespace;
        return $this;
    }
}
