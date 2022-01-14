<?php

namespace src\listeners\lead;

use src\events\lead\LeadableEventInterface;
use src\services\lead\qcall\Config;
use src\services\lead\qcall\FindPhoneParams;
use src\services\lead\qcall\FindWeightParams;
use src\services\lead\qcall\QCallService;

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
                new FindWeightParams($lead->project_id, $lead->status),
                $lead->offset_gmt,
                new FindPhoneParams($lead->project_id, $lead->l_dep_id)
            );
        } catch (\Throwable $e) {
            \Yii::error($e, 'LeadQcallAddListener');
        }
    }
}
