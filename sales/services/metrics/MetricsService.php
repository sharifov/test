<?php

namespace sales\services\metrics;

use common\components\Metrics;

/**
 * Class MetricsService
 *
 * @property Metrics $metrics
 */
class MetricsService
{
    private Metrics $metrics;

    /**
     * @param Metrics $metrics
     */
    public function __construct(Metrics $metrics)
    {
        $this->metrics = $metrics;
    }

    public function addQuoteSearchHistogram(float $timeStart, string $type): void
    {
        $seconds = round(microtime(true) - $timeStart, 1);
        $this->metrics->histogramMetric('quote_search', $seconds, ['type' => $type]);
    }

    public function addQuoteSearchCounter(string $type): void
    {
        $this->metrics->counterMetric('quote_search', '', ['type' => $type]);
    }
}
