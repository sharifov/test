<?php

namespace modules\lead\src\services;

use common\models\Lead;
use common\models\ProfitSplit;
use frontend\models\ProfitSplitForm;

class LeadProfitSplit
{
    public function save(Lead $lead, int $newOwnerId = null, $percent = 100): void
    {
        if (!$newOwnerId) {
            return;
        }

        $errors = [];
        $splitForm = new ProfitSplitForm($lead);
        $splitForm->setZeroPercent(true);
        $profitSplitData = [];

        /** @var ProfitSplit $profitSplit */
        foreach ($lead->getAllProfitSplits() as $profitSplit) {
            if ($profitSplit->ps_user_id === $newOwnerId) {
                return;
            }
            $profitSplitData[$profitSplit->ps_id] = [
                'ps_user_id' => $profitSplit->ps_user_id,
                'ps_percent' => $profitSplit->ps_percent,
            ];
        }
        $profitSplitData['new' . $lead->id] = [
            'ps_user_id' => $newOwnerId,
            'ps_percent' => $percent,
        ];
        $load = $splitForm->loadModels([
            'ProfitSplit' => $profitSplitData,
        ]);

        if ($load) {
            $splitForm->save($errors);
        }

        if (!empty($errors)) {
            \Yii::error($errors, 'Leads:LeadSoldSplit');
        }
    }
}
