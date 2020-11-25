<?php

namespace sales\behaviors;

use yii\base\Behavior;
use yii\queue\ExecEvent;
use yii\queue\Queue;

/**
 * Class JobIdAccessBehavior
 */
class JobIdAccessBehavior extends Behavior
{
     private $jobId;

    public function events(): array
    {
        return [
           Queue::EVENT_BEFORE_EXEC => 'shareId',
           Queue::EVENT_AFTER_EXEC => 'unsetId',
           Queue::EVENT_AFTER_ERROR => 'unsetId',
        ];
    }

    public function shareId(ExecEvent $event): void
    {
         $this->jobId = $event->id;
    }

    public function unsetId()
    {
          $this->jobId = null;
    }

      /**
      *  @return null|int|string
      **/
    public function getJobId()
    {
        return $this->jobId;
    }
}
