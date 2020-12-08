<?php

namespace sales\listeners\lead;

use sales\events\lead\LeadableEventInterface;
use sales\services\lead\qcall\Config;
use sales\services\lead\qcall\FindPhoneParams;
use sales\services\lead\qcall\FindWeightParams;
use sales\services\lead\qcall\QCallService;

/**
 * Class LeadQcallProcessingListener
 *
 * @property  QCallService $service
 */
class LeadQcallProcessingListener
{
    private $service;

    public function __construct(QCallService $service)
    {
        $this->service = $service;
    }

    public function handle(LeadableEventInterface $event): void
    {
        $lead = $event->getLead();

        $config = new Config($lead->status, 0);

        try {
            if (!$qConfig = $this->service->findConfig($config)) {
                $this->service->remove($lead->id);
                return;
            }

            if ($qCall = $lead->leadQcall) {
                $this->service->updateInterval(
                    $qCall,
                    $config,
                    $lead->offset_gmt,
                    new FindPhoneParams($lead->project_id, $lead->l_dep_id),
                    new FindWeightParams($lead->project_id, $lead->status)
                );
                return;
            }

            $this->service->create(
                $lead->id,
                $config,
                new FindWeightParams($lead->project_id, $lead->status),
                $lead->offset_gmt,
                new FindPhoneParams($lead->project_id, $lead->l_dep_id)
            );
        } catch (\Throwable $e) {
            \Yii::error($e, 'LeadQcallProcessingListener');
        }
    }
}
