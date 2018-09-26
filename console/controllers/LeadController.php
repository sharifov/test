<?php

namespace console\controllers;

use common\models\Lead;
use yii\console\Controller;
use yii\helpers\Console;

class LeadController extends Controller
{
    public function actionUpdateIpInfo()
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        $leads = Lead::find()
            ->where(['offset_gmt' => null])
            ->andWhere(['status' => [Lead::STATUS_PENDING, Lead::STATUS_PROCESSING]])
            ->andWhere(['IS NOT', 'request_ip', null])
            ->orderBy(['id' => SORT_DESC])
            ->limit(3)->all();

            //print_r($leads->createCommand()->getRawSql());

        if($leads) {
            foreach ($leads as $lead) {
                if($lead->updateIpInfo()) {
                    echo $lead->id." OK\r\n";
                } else {
                    echo $lead->id." Error\r\n";
                }
            }

        }
        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }


}