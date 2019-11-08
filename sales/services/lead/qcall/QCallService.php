<?php

namespace sales\services\lead\qcall;

use Yii;
use common\models\LeadQcall;
use common\models\QcallConfig;
use yii\helpers\VarDumper;

class QCallService
{
    public function create($status, $callCount)
    {
        if (!($config = QcallConfig::getByStatusCall($status, $callCount))) {
            return;
        }
    }

    public function update(int $status, int $callCount, LeadQcall $qCall): void
    {
        if (!($config = QcallConfig::getByStatusCall($status, $callCount))) {
            try {
                $qCall->delete();
            } catch (\Throwable $e) {
                Yii::error($e, 'QCallService:update:qCall:delete');
            }
            return;
        }

        $date = (new CalculateDateService())->calculate(
            $config->qc_client_time_enable,
            $this->offset_gmt,
            $config->qc_time_from,
            $config->qc_time_to
        );

        $qCall->lqc_dt_from = $date->from;
        $qCall->lqc_dt_to = $date->to;

        if (!$qCall->save()) {
            Yii::error(VarDumper::dumpAsString($lq->errors), 'Lead:createOrUpdateQCall:LeadQcall:save');
            return true;
        }

    }
}
