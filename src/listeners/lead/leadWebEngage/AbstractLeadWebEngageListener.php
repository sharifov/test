<?php

namespace src\listeners\lead\leadWebEngage;

use common\components\jobs\WebEngageLeadRequestJob;
use modules\webEngage\settings\WebEngageSettings;
use src\events\lead\LeadableEventInterface;
use src\helpers\app\AppHelper;
use Yii;

/**
 * Class AbstractLeadWebEngageListener
 *
 * @property string $eventName
 */
abstract class AbstractLeadWebEngageListener
{
    public function handle(LeadableEventInterface $event): void
    {
        try {
            $lead = $event->getLead();
            if ((new WebEngageSettings())->isEnabled()) {
                $job = new WebEngageLeadRequestJob($lead->id, $this->getEventName());
                Yii::$app->queue_job->priority(100)->push($job);
            }
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'Listeners:AbstractLeadWebEngageListener:handle'
            );
        }
    }

    public function getEventName(): string
    {
        if ($this->eventName === null) {
            throw new \RuntimeException('EventName is empty');
        }
        return $this->eventName;
    }
}
