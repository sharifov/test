<?php

namespace sales\listeners\lead;

use sales\events\lead\LeadableEventInterface;
use sales\services\lead\qcall\Config;
use sales\services\lead\qcall\QCallService;

/**
 * Class LeadQcallAddListener
 *
 * @property  QCallService $service
 */
class LeadQcallAddListener
{
    private $service;

    /**
     * @param QCallService $service
     */
    public function __construct(QCallService $service)
    {
        $this->service = $service;
    }

    /**
     * @param LeadableEventInterface $event
     */
    public function handle(LeadableEventInterface $event): void
    {
        $lead = $event->getLead();
        try {
            $this->service->create(
                $lead->id,
                new Config(
                    $lead->status,
                    $lead->getCountOutCallsLastFlow()
                ),
                ($lead->project_id * 10),
                $lead->offset_gmt
            );
        } catch (\Throwable $e) {
            \Yii::error($e, 'LeadQcallAddListener');
        }
    }
}
