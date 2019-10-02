<?php


namespace console\controllers;


use common\models\Lead;
use sales\temp\LeadFlowUpdate;
use yii\console\Controller;

class UpdateLeadFlow extends Controller
{
    public function actionUpdate()
    {

        /*
        $lead = Lead::find()->andWhere(['id' => 269691])
            ->with([
                'leadLogs' => function($query) {
                $query->orderBy(['created' => SORT_ASC, 'id' => SORT_ASC])->asArray();
                },
                'leadFlows' => function($query) {
                    $query->orderBy(['created' => SORT_ASC, 'id' => SORT_ASC])->indexBy('created');
                },
                'reasons' => function($query) {
                    $query->orderBy(['created' => SORT_ASC, 'id' => SORT_ASC])->indexBy('created')->asArray();
                },
            ])
            ->one();

        LeadFlowUpdate::update($lead);
*/

        $start = time();

        $query = Lead::find()
            ->orderBy(['created' => SORT_ASC])
            ->with([
                'leadLogs' => function($query) {
                    $query->orderBy(['created' => SORT_ASC, 'id' => SORT_ASC])->asArray();
                },
                'leadFlows' => function($query) {
                    $query->orderBy(['created' => SORT_ASC, 'id' => SORT_ASC])->indexBy('created');
                },
                'reasons' => function($query) {
                    $query->orderBy(['created' => SORT_ASC, 'id' => SORT_ASC])->indexBy('created')->asArray();
                },
            ])
            ->limit(10)->offset(3);

        foreach ($query->each(500) as $lead) {
            $timeStart = time();
            LeadFlowUpdate::update($lead);
            $duration = time() - $timeStart;
            echo 'Update LeadFLow for Lead: ' . $lead->id . ' :Duration: ' . $duration . '<br>';
        }

        echo 'All Duration: ' . (time() - $start) . '<br>';
        die;
    }

}