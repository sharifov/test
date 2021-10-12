<?php

namespace sales\listeners\lead;

use frontend\models\ProfitSplitForm;
use sales\events\lead\LeadSoldEvent;
use Yii;

class LeadSoldSplitListener
{
    public function handle(LeadSoldEvent $event): void
    {
        try {
            $errors = [];
            $lead = $event->lead;
            $splitForm = new ProfitSplitForm($lead);
            $load = $splitForm->loadModels([
                'ProfitSplit' => [
                    'new' . $event->lead->id => [
                        'ps_user_id' => $lead->employee_id ?? $event->newOwnerId,
                        'ps_percent' => '100',
                    ]
                ],
            ]);

            if ($load) {
                $splitForm->save($errors);
            }
            if (!empty($errors)) {
                Yii::error($errors, 'Listeners:LeadSoldSplitListener');
            }
        } catch (\Throwable $exception) {
            Yii::error($exception, 'Listeners:LeadSoldSplitListener');
        }
    }
}
