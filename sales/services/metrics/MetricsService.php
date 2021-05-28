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

    public function addQuoteSearchHistogram(float $timeStart, string $type, array $buckets = []): void
    {
        $seconds = round(microtime(true) - $timeStart, 1);
        $buckets = empty($buckets) ? [2, 4, 6, 8, 10, 13, 16, 20, 25, 30, 40, 60, 90] : $buckets;
        $this->metrics->histogramMetric('quote_search', $seconds, ['type' => $type], '', '', $buckets);
    }

    public function addQuoteSearchCounter(string $type): void
    {
        $this->metrics->counterMetric('quote_search', '', ['type' => $type]);
    }

    public function addJobExecuteHistogram(float $timeStart, string $jobName, array $buckets = []): void
    {
        $seconds = round(microtime(true) - $timeStart, 1);
        $buckets = empty($buckets) ? [1, 3, 5, 7, 10, 15, 30, 45, 60, 90] : $buckets;
        $this->metrics->histogramMetric('job_execute', $seconds, ['jobName' => $jobName], '', '', $buckets);
    }
}
