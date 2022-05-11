<?php

namespace src\listeners\lead;

use common\components\jobs\LeadObjectSegmentJob;
use modules\featureFlag\FFlag;
use modules\objectSegment\src\contracts\ObjectSegmentKeyContract;
use modules\objectSegment\src\object\dto\LeadObjectSegmentDto;
use src\events\lead\LeadCreatedEvent;
use Yii;

/**
 * Class LeadCreatedEventListener
 */
class LeadCreatedEventListener
{
    private $jobPriority = 100;
    /**
     * @param LeadCreatedEvent $event
     */
    public function handle(LeadCreatedEvent $event): void
    {
        /** @fflag FFlag::FF_KEY_OBJECT_SEGMENT_MODULE_ENABLE, Object Segment module enable/disable */
        if (Yii::$app->ff->can(FFlag::FF_KEY_OBJECT_SEGMENT_MODULE_ENABLE)) {
            $job = new LeadObjectSegmentJob($event->lead);
            \Yii::$app->queue_job->priority($this->jobPriority)->push($job);
        }
    }
}
