<?php

namespace src\listeners\lead;

use common\models\TipsSplit;
use src\events\lead\LeadSoldEvent;
use src\repositories\lead\LeadTipsSplitRepository;

/**
 * @property-read LeadTipsSplitListener $repository
 */
class LeadTipsSplitListener
{
    private LeadTipsSplitRepository $repository;

    public function __construct(LeadTipsSplitRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(LeadSoldEvent $event): void
    {
        try {
            if ($event->lead->employee_id && $event->lead->tips) {
                $tips = TipsSplit::create($event->lead->id, $event->lead->employee_id, 100);
                $this->repository->save($tips);
            }
        } catch (\RuntimeException $e) {
            \Yii::warning([
                'message' => $e->getMessage(),
                'leadGid' => $event->lead->gid,
                'userId' => $event->lead->employee_id,
            ], 'LeadSoldEvent::LeadTipsSplitListener::RuntimeException');
        }
    }
}
