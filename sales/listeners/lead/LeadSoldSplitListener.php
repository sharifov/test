<?php

namespace sales\listeners\lead;

use modules\lead\src\services\LeadProfitSplit;
use sales\events\lead\LeadSoldEvent;
use Yii;

class LeadSoldSplitListener
{
    /**
     * @var LeadProfitSplit
     */
    protected $leadProfitSplitService;

    public function __construct(LeadProfitSplit $leadProfitSplitService)
    {
        $this->leadProfitSplitService = $leadProfitSplitService;
    }

    public function handle(LeadSoldEvent $event): void
    {
        try {
            $lead = $event->lead;
            $this->leadProfitSplitService->save($lead, $lead->employee_id ?? $event->newOwnerId);
        } catch (\Throwable $exception) {
            Yii::error($exception, 'Listeners:LeadSoldSplitListener');
        }
    }
}
