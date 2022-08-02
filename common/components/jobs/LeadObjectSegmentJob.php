<?php

namespace common\components\jobs;

use common\models\Lead;
use modules\featureFlag\FFlag;
use modules\objectSegment\src\contracts\ObjectSegmentKeyContract;
use modules\objectSegment\src\contracts\ObjectSegmentListContract;
use modules\objectSegment\src\entities\ObjectSegmentList;
use modules\objectSegment\src\object\dto\LeadObjectSegmentDto;
use src\helpers\app\AppHelper;
use src\model\leadBusinessExtraQueue\service\LeadBusinessExtraQueueService;
use src\model\leadData\entity\LeadData;
use src\model\leadData\entity\LeadDataQuery;
use src\model\leadData\repository\LeadDataRepository;
use src\model\leadDataKey\services\LeadDataKeyDictionary;
use yii\helpers\ArrayHelper;
use yii\queue\JobInterface;

class LeadObjectSegmentJob extends BaseJob implements JobInterface
{
    public Lead $lead;

    public function __construct(Lead $lead, ?float $timeStart = null, array $config = [])
    {
        $this->lead = $lead;
        parent::__construct($timeStart, $config);
    }

    /**
     * @param $queue
     */
    public function execute($queue): void
    {
        $this->waitingTimeRegister();
        $logData = [
            'leadId' => $this->lead->id,
        ];
        try {
            $leadObjectDto = new LeadObjectSegmentDto($this->lead);
            \Yii::$app->objectSegment->segment($leadObjectDto, ObjectSegmentKeyContract::TYPE_KEY_LEAD);
            if ($this->lead->isProcessing()) {
                /** @fflag FFlag::FF_KEY_BEQ_ENABLE, Business Extra Queue enable */
                if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_BEQ_ENABLE) && $this->lead->isBusinessType()) {
                    LeadBusinessExtraQueueService::addLeadBusinessExtraQueueJob($this->lead, 'Added new Business Extra Queue Countdown');
                }
            }

            if (!LeadDataQuery::getByLeadAndKey($this->lead->id, LeadDataKeyDictionary::KEY_LEAD_OBJECT_SEGMENT)->exists()) {
                $leadData = LeadData::create(
                    $this->lead->id,
                    LeadDataKeyDictionary::KEY_LEAD_OBJECT_SEGMENT,
                    ObjectSegmentListContract::OBJECT_SEGMENT_LIST_KEY_LEAD_TYPE_SIMPLE
                );
                (new LeadDataRepository())->save($leadData);
            }
        } catch (\RuntimeException | \DomainException $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
            \Yii::warning($message, 'LeadObjectSegmentJob:execute:Exception');
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
            \Yii::error($message, 'LeadObjectSegmentJob:execute:Throwable');
        }
    }
}
