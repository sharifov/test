<?php

namespace sales\listeners\lead;

use sales\events\lead\LeadTaskEvent;
use sales\services\task\LeadTaskService;

/**
 * Class LeadTaskEventListener
 *
 * @property LeadTaskService $service
 */
class LeadTaskEventListener
{
    private $service;

    /**
     * @param LeadTaskService $service
     */
    public function __construct(LeadTaskService $service)
    {
        $this->service = $service;
    }

    /**
     * @param LeadTaskEvent $event
     */
    public function handle(LeadTaskEvent $event): void
    {
        $this->service->createLeadTasks($event->lead);
    }

}
