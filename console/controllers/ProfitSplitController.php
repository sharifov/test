<?php

namespace console\controllers;

use common\models\Lead;
use modules\lead\src\services\LeadProfitSplit;
use src\helpers\app\AppHelper;
use yii\console\Controller;
use Yii;

class ProfitSplitController extends Controller
{
    /**
     * @var LeadProfitSplit
     */
    private $leadProfitSplitService;

    public function __construct(
        $id,
        $module,
        LeadProfitSplit $leadProfitSplitService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->leadProfitSplitService = $leadProfitSplitService;
    }

    public function actionOwnerToProfitSplit(): void
    {
        $report = [];
        $leads = Lead::find()
            ->where('employee_id IS NOT NULL')
            ->andWhere(['status' => Lead::STATUS_SOLD]);

        foreach ($leads->batch(100) as $leadsBatch) {
            /** @var Lead $lead */
            foreach ($leadsBatch as $lead) {
                try {
                    if (in_array($lead->employee_id, array_column($lead->profitSplits, 'ps_user_id'))) {
                        continue;
                    }
                    $usedPercent = array_sum(array_column($lead->profitSplits, 'ps_percent'));

                    $this->leadProfitSplitService->save($lead, $lead->employee_id, 100 - $usedPercent);
                    $report[$lead->id] = 'Lead: ' . $lead->id . ' -> added profit split for owner';
                } catch (\Throwable $e) {
                    $report[] = 'Lead: ' . $lead->id . ' profit split not updated';
                    Yii::error(
                        AppHelper::throwableLog($e),
                        'ProfitSplitController:actionOwnerToProfitSplit:Lead:save'
                    );
                }
            }
        }

        foreach ($report as $item) {
            echo $item . PHP_EOL;
        }

        $message = '0 profit splits ready';
        if (count($report) > 0) {
            $message = count($report) . ' lead profit splits updated. [' . implode(', ', array_keys($report)) . ']';
        }
        Yii::info($message, 'info\OwnerToProfitSplit');
    }
}
