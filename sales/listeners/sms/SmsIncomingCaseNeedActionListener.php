<?php

namespace sales\listeners\sms;

use sales\services\cases\CasesManageService;
use sales\services\sms\incoming\SmsIncomingEvent;

/**
 * Class SmsIncomingCaseNeedActionListener
 *
 * @property CasesManageService $service
 */
class SmsIncomingCaseNeedActionListener
{
    private $service;

    public function __construct(CasesManageService $service)
    {
        $this->service = $service;
    }

    public function handle(SmsIncomingEvent $event): void
    {
        if (!$event->sms->s_case_id) {
            return;
        }

        $this->service->needAction($event->sms->s_case_id);
    }
}
