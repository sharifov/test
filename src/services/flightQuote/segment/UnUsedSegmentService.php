<?php

namespace src\services\flightQuote\segment;

use common\components\jobs\SendNotificationToClientJob;
use common\models\Project;
use DateInterval;
use DateTime;
use Exception;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use src\dto\flightQuote\UnUsedSegmentDTO;
use src\entities\cases\CaseEventLog;
use src\entities\cases\Cases;
use Yii;

/**
 * Class UnUsedSegmentService
 */
class UnUsedSegmentService
{
    /**
     * @param ProductQuote $productQuote
     * @param ProductQuoteChange $productQuoteChange
     * @param Cases $case
     * @return UnUsedSegmentDTO|null
     */
    public function getUnUsedSegmentData(ProductQuote $productQuote, ProductQuoteChange $productQuoteChange, Cases $case): ?UnUsedSegmentDTO
    {
        $segmentData = null;

        if ($productQuote->isFlight() && $flightQuote = $productQuote->flightQuote) {
            $now = new DateTime();
            $firstSegment = $flightQuote->getFlightQuoteSegments()
                ->where('DATE_FORMAT(fqs_departure_dt, "%Y-%m-%d") > :now', [':now' => $now->format('Y-m-d')])
                ->limit(1)
                ->one();

            if ($firstSegment) {
                $data = [
                    'caseId' => $case->cs_id ?? null,
                    'projectId' => $case->project->id ?? null,
                    'projectKey' => $case->project->api_key ?? null,
                    'productQuoteChangeId' => $productQuoteChange->pqc_id ?? null,
                    'productQuoteId' => $productQuote->pq_id,
                    'flightQuoteSegmentId' => $firstSegment['fqs_id'],
                    'departureDt' => $firstSegment['fqs_departure_dt'],
                ];

                $segmentData = new UnUsedSegmentDTO($data);
            }
        }

        return $segmentData;
    }

    /**
     * @param UnUsedSegmentDTO $segment
     * @return string|null
     * @throws Exception
     */
    public function calculateNextDateOfNotification(UnUsedSegmentDTO $segment): ?string
    {
        $notificationIntervals = $this->getNotificationIntervals($segment);
        $nextNotification = null;

        if ($notificationIntervals) {
            $departureDate = new DateTime($segment->departureDt);
            $now = new DateTime();
            $daysToFlight = $departureDate->diff($now)->days;

            foreach ($notificationIntervals['notification_intervals'] as $setting) {
                if ($daysToFlight > 0 && $this->isBetween($daysToFlight, $setting)) {
                    $nextNotification = $now->add(new DateInterval('P' . $setting['frequency'] . 'D'))->format('Y-m-d H:i:s');
                }
            }
        }

        return $nextNotification;
    }

    /**
     * @param UnUsedSegmentDTO $segment
     * @return array|null
     */
    public function getNotificationIntervals(UnUsedSegmentDTO $segment): ?array
    {
        $project = Project::findOne($segment->projectId);

        if (!$project) {
            Yii::warning([
                'message' => 'Not found project.',
                'projectId' => $segment->projectId,
            ], 'UnUsedSegmentService::getNotificationIntervals');
            return null;
        }

        $notificationIntervals = $project->getScheduleChangeNotificationIntervals();

        if (!$notificationIntervals || !isset($notificationIntervals['notification_intervals']) || count($notificationIntervals['notification_intervals']) == 0) {
            Yii::warning([
                'message' => 'Not found schedule change notification intervals.',
                'projectId' => $segment->projectId,
            ], 'UnUsedSegmentService::getNotificationIntervals');

            return null;
        }

        return $notificationIntervals;
    }

    /**
     * @param int $number
     * @param array $setting
     * @return bool
     */
    public function isBetween(int $number, array $setting): bool
    {
        if (!isset($setting['days_from']) || !isset($setting['days_to']) || !isset($setting['frequency'])) {
            return false;
        }
        return (($number >= $setting['days_from']) && ($number <= $setting['days_to']));
    }

    /**
     * @param UnUsedSegmentDTO $unUsedSegment
     * @return void
     * @throws Exception
     */
    public function addToQueueJob(UnUsedSegmentDTO $unUsedSegment): void
    {
        $nextDateOfNotification = $this->calculateNextDateOfNotification($unUsedSegment);
        if (!empty($nextDateOfNotification)) {
            $nextDateOfNotification = new DateTime($nextDateOfNotification);
            $now = new DateTime();

            $delayJob = $nextDateOfNotification->getTimestamp() - $now->getTimestamp();

            $job = new SendNotificationToClientJob();
            $job->unUsedSegment = $unUsedSegment;
            $jobId = Yii::$app->queue_email_job->delay($delayJob)->priority(150)->push($job);

            $case = Cases::findOne($unUsedSegment->caseId);
            if ($case && $jobId) {
                $case->addEventLog(
                    CaseEventLog::RE_PROTECTION_ADDED_TO_REMAINDER_QUEUE,
                    'Change Product Quote List added to queue email remainder notification',
                    ['productQuoteChangeId' => $unUsedSegment->productQuoteChangeId, 'jobId' => $jobId],
                    CaseEventLog::CATEGORY_INFO
                );
            }
        }
    }
}
