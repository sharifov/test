<?php


namespace console\controllers;


use common\models\Lead;
use sales\temp\LeadFlowUpdate;
use yii\console\Controller;

class UpdateLeadFlowController extends Controller
{

    public function actionUpdate($offset = null, $limit = null)
    {

        $limit = (int)$limit;
        $offset = (int)$offset;

        $start = time();

        $query = Lead::find()
            ->orderBy(['created' => SORT_ASC])
            ->with([
                'leadLogs' => function ($query) {
                    $query->orderBy(['created' => SORT_ASC, 'id' => SORT_ASC])->asArray();
                },
                'leadFlows' => function ($query) {
                    $query->orderBy(['created' => SORT_ASC, 'id' => SORT_ASC])->indexBy('created');
                },
                'reasons' => function ($query) {
                    $query->orderBy(['created' => SORT_ASC, 'id' => SORT_ASC])->indexBy('created')->asArray();
                },
            ]);

        if ($offset) {
            $query->offset($offset);
        }
        if ($limit) {
            $query->limit($limit);
        }

        $count = (clone $query)->count();

        echo 'All count in DB: ' . $count . PHP_EOL;

        if (($limit + $offset) >= $count) {
            $count -= $offset;
        } else {
            $count = $limit ?: ($count - $offset);
        }

        echo 'Remaining for update: ' . $count . PHP_EOL;

        $number = 0;
        $already = 0;
        $timer1 = time();
        echo 'Begin. ';
        foreach ($query->each(500) as $lead) {
            if ($number === 1000) {
                $number = 0;
                echo 'Done: ' . (time() - $timer1) . ' sec' . PHP_EOL .  'Remaining: ' . ($count - $already) .  PHP_EOL;
                $timer1 = time();
                echo 'Begin. ';
            }
            $number++;
            $timeStart = time();
            $report = LeadFlowUpdate::update($lead);
            $duration = time() - $timeStart;
          //  echo 'Update LeadFLow for Lead: ' . $lead->id . ' :Duration: ' . $duration . PHP_EOL;
            if ($report) {
                echo PHP_EOL . 'Errors: ' . PHP_EOL;
                foreach ($report as $item) {
                    echo $item . PHP_EOL;
                }
            }
            $already++;
        }
        echo 'Done: ' . (time() - $timer1) . ' sec' . PHP_EOL;
        echo 'All Duration: ' . (time() - $start) . PHP_EOL;
    }

}