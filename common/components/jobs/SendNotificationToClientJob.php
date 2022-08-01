<?php

namespace common\components\jobs;

use DateTime;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteChange\events\ProductQuoteChangeClientRemainderNotificationEvent;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use src\dispatchers\EventDispatcher;
use src\dto\flightQuote\UnUsedSegmentDTO;
use src\entities\cases\Cases;
use src\entities\cases\CasesStatus;
use src\helpers\app\AppHelper;
use src\repositories\NotFoundException;
use src\services\flightQuote\segment\UnUsedSegmentService;
use Yii;
use yii\queue\JobInterface;

/**
 * Class SendNotificationToClientJob
 *
 * @property UnUsedSegmentDTO $unUsedSegment
 * @property UnUsedSegmentService $unUsedSegmentService
 */
class SendNotificationToClientJob extends BaseJob implements JobInterface
{
    public UnUsedSegmentDTO $unUsedSegment;

    /**
     * @param $queue
     * @throws \yii\base\InvalidConfigException
     */
    public function execute($queue): void
    {
        $this->waitingTimeRegister();

        $eventDispatcher = Yii::createObject(EventDispatcher::class);
        $unUsedSegmentService = Yii::createObject(UnUsedSegmentService::class);

        try {
            $productQuote = ProductQuote::findOne($this->unUsedSegment->productQuoteId);
            if (!$productQuote) {
                throw new NotFoundException('ProductQuote not found. Id (' . $this->unUsedSegment->productQuoteId . ')');
            }

            $productQuoteChange = ProductQuoteChange::findOne($this->unUsedSegment->productQuoteChangeId);
            if (!$productQuoteChange) {
                throw new NotFoundException('ProductQuoteChange not found. Id (' . $this->unUsedSegment->productQuoteChangeId . ')');
            }

            $case = Cases::findOne($this->unUsedSegment->caseId);
            if (!$case) {
                throw new NotFoundException('Case not found. Id (' . $this->unUsedSegment->caseId . ')');
            }

            if ($case->isError() || $case->isTrash() || $case->isAwaiting() || $case->isSolved()) {
                $caseStatus = '';
                if (array_key_exists($case->cs_status, CasesStatus::STATUS_LIST)) {
                    $caseStatus = CasesStatus::STATUS_LIST[$case->cs_status];
                }

                $case->addEventLog(null, 'Remainder notification not sent. Case status (' . $caseStatus . ')');
                return;
            }

            if ($productQuoteChange->isPending() && $productQuote->isNew()) {
                $eventDispatcher->dispatch(new ProductQuoteChangeClientRemainderNotificationEvent($productQuoteChange->pqc_id));

                $nextDateOfNotification = $unUsedSegmentService->calculateNextDateOfNotification($this->unUsedSegment);
                if (!empty($nextDateOfNotification)) {
                    $nextDateOfNotification = new DateTime($nextDateOfNotification);
                    $now = new DateTime();
                    $delayJob = $nextDateOfNotification->getTimestamp() - $now->getTimestamp();

                    $this->repeatJob($this->unUsedSegment, $delayJob);
                }
            }
        } catch (\RuntimeException | \DomainException | NotFoundException $throwable) {
            $message = AppHelper::throwableLog($throwable);
            $message['caseId'] = $this->unUsedSegment->caseId;
            $message['projectId'] = $this->unUsedSegment->projectId;
            $message['productQuoteChangeId'] = $this->unUsedSegment->productQuoteChangeId;
            $message['productQuoteId'] = $this->unUsedSegment->productQuoteId;
            $message['flightQuoteSegmentId'] = $this->unUsedSegment->flightQuoteSegmentId;
            \Yii::warning($message, 'SendNotificationToClientJob:execute:Exception');
        } catch (\Throwable $throwable) {
            $message = AppHelper::throwableLog($throwable);
            $message['caseId'] = $this->unUsedSegment->caseId;
            $message['projectId'] = $this->unUsedSegment->projectId;
            $message['productQuoteChangeId'] = $this->unUsedSegment->productQuoteChangeId;
            $message['productQuoteId'] = $this->unUsedSegment->productQuoteId;
            $message['flightQuoteSegmentId'] = $this->unUsedSegment->flightQuoteSegmentId;
            \Yii::error($message, 'SendNotificationToClientJob:execute:Throwable');
        }
    }

    /**
     * Repeat job
     * @param UnUsedSegmentDTO $unUsedSegment
     * @param int $delayJob
     */
    private function repeatJob(UnUsedSegmentDTO $unUsedSegment, int $delayJob): void
    {
        $job = new SendNotificationToClientJob();
        $job->unUsedSegment = $unUsedSegment;
        Yii::$app->queue_email_job->delay($delayJob)->priority(150)->push($job);
    }
}
