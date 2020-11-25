<?php

namespace sales\model\visitorLog\useCase;

use common\models\Client;
use common\models\Lead;
use common\models\Sources;
use common\models\VisitorLog;
use thamtech\uuid\helpers\UuidHelper;
use yii\helpers\VarDumper;
use Yii;

class CreateVisitorLog
{
    public function create(Client $client, Lead $lead): ?int
    {
        $sourceCid = null;
        if ($source = Sources::find()->select(['cid'])->andWhere(['id' => $lead->source_id])->asArray()->limit(1)->one()) {
            $sourceCid = $source['cid'];
        }

        if ($lastVisitorLog = VisitorLog::find()->andWhere(['vl_client_id' => $client->id, 'vl_project_id' => $lead->project_id])->orderBy(['vl_visit_dt' => SORT_DESC])->limit(1)->one()) {
            if ($sourceCid && $lastVisitorLog->vl_source_cid === $sourceCid) {
                return $lastVisitorLog->vl_id;
            }

            $log = new VisitorLog([
                'vl_source_cid' => $sourceCid,
                'vl_ga_client_id' => $lastVisitorLog->vl_ga_client_id,
                'vl_ga_user_id' => $client->uuid,
                'vl_visit_dt' => date('Y-m-d H:i:s'),
                'vl_lead_id' => $lead->id,
                'vl_project_id' => $lead->project_id,
                'vl_client_id' => $client->id,
            ]);

            if (!$log->save()) {
                Yii::error(
                    'Cant save visitor_log. Point:1 LeadId: ' . $lead->id . ' Errors: ' . VarDumper::dumpAsString($log->getErrors()),
                    'CreateVisitorLog:create:log:save'
                );
                return null;
            }

            return $log->vl_id;
        }

        $log = new VisitorLog([
            'vl_source_cid' => $sourceCid,
            'vl_ga_client_id' => UuidHelper::uuid(),
            'vl_ga_user_id' => $client->uuid,
            'vl_visit_dt' => date('Y-m-d H:i:s'),
            'vl_lead_id' => $lead->id,
            'vl_project_id' => $lead->project_id,
            'vl_client_id' => $client->id,
        ]);

        if (!$log->save()) {
            Yii::error(
                'Cant save visitor_log. Point:2  LeadId: ' . $lead->id . ' Errors: ' . VarDumper::dumpAsString($log->getErrors()),
                'CreateVisitorLog:create:log:save'
            );
            return null;
        }

        return $log->vl_id;
    }
}
