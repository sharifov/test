<?php

namespace sales\model\callLog\services;

use common\models\Call;
use common\models\CallUserAccess;
use common\models\Conference;
use sales\model\callLog\entity\callLog\CallLog;
use sales\model\callLog\entity\callLogCase\CallLogCase;
use sales\model\callLog\entity\callLogLead\CallLogLead;
use sales\model\callLog\entity\callLogQueue\CallLogQueue;
use sales\model\callLog\entity\callLogRecord\CallLogRecord;
use sales\model\conference\useCase\recordingStatusCallBackEvent\ConferenceRecordingStatusCallbackForm;
use Yii;
use yii\helpers\VarDumper;

/**
 * Class CallLogConferenceTransferService
 *
 */
class CallLogConferenceTransferService
{
    public function saveRecord(int $conferenceId, ConferenceRecordingStatusCallbackForm $form): void
    {
        if (!$log = CallLog::findOne(['cl_conference_id' => $conferenceId])) {
            return;
        }

        $callLogRecord = new CallLogRecord([
            'clr_cl_id' => $log->cl_id,
            'clr_duration' => $form->RecordingDuration,
            'clr_record_sid' => $form->RecordingSid,
        ]);

        try {
            if (!$callLogRecord->save()) {
                Yii::error(VarDumper::dumpAsString([
                    'category' => 'saveRecord',
                    'model' => $callLogRecord->toArray(),
                    'errors' => $callLogRecord->getErrors()
                ]), 'CallLogConferenceTransferService');
            }
        } catch (\Throwable $e) {
            Yii::error(VarDumper::dumpAsString(['category' => 'saveRecord', 'error' => $e->getMessage()]), 'CallLogConferenceTransferService');
        }
    }

    public function transfer(Call $call, Conference $conference): void
    {
        $debugEnable = Yii::$app->params['settings']['call_log_debug_enable'] ?? false;
        if ($debugEnable) {
            Yii::info(VarDumper::dumpAsString(['call' => $call->getAttributes(), 'conference' => $conference]), 'info\DebugCallLogConference');
        }

        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $log = new CallLog();
            $log->cl_id = $call->c_id;
            $log->cl_conference_id = $call->c_conference_id;
            if ($call->isIn()) {
                $log->cl_call_created_dt = $call->c_queue_start_dt;
            } elseif ($call->isOut()) {
                $log->cl_call_created_dt = $call->c_created_dt;
            }
            $log->cl_call_finished_dt = $conference->cf_end_dt;
            $log->cl_duration = abs(strtotime($log->cl_call_finished_dt) - strtotime($log->cl_call_created_dt));
            $time = strtotime($log->cl_call_created_dt);
            $log->cl_year = date('Y', $time);
            $log->cl_month = date('m', $time);
            $log->cl_call_sid = $call->c_call_sid;
            $log->cl_category_id = $call->c_source_type_id;
            $log->cl_client_id = $call->c_client_id;
            $log->cl_department_id = $call->c_dep_id;
            $log->cl_group_id = $call->c_group_id;
            $log->cl_is_transfer = $call->c_is_transfer;//todo
            $log->cl_phone_from = $call->c_from;
            $log->cl_phone_to = $call->c_to;
//        $log->cl_price;//todo
            $log->cl_project_id = $call->c_project_id;
            $log->cl_status_id = $call->isEnded() ? $call->c_status_id : Call::STATUS_COMPLETED;
            $log->cl_type_id = $call->c_call_type_id;
            $log->cl_user_id = $call->c_created_user_id;

            if (!$log->save()) {
                Yii::error($log->getErrors());
                throw new \RuntimeException(VarDumper::dumpAsString(['model' => $log->toArray(), 'message' => $log->getErrors()]));
            }

            if ($call->c_lead_id) {
                $callLogLead = new CallLogLead([
                    'cll_cl_id' => $log->cl_id,
                    'cll_lead_id' => $call->c_lead_id,
                ]);
                if (!$callLogLead->save()) {
                    throw new \RuntimeException(VarDumper::dumpAsString(['model' => $callLogLead->toArray(), 'message' => $callLogLead->getErrors()]));
                }
            }

            if ($call->c_case_id) {
                $callLogCase = new CallLogCase([
                    'clc_cl_id' => $log->cl_id,
                    'clc_case_id' => $call->c_case_id
                ]);
                if (!$callLogCase->save()) {
                    throw new \RuntimeException(VarDumper::dumpAsString(['model' => $callLogCase->toArray(), 'message' => $callLogCase->getErrors()]));
                }
            }

            if ($call->isIn()) {
                if ($parentCall = $call->cParent) {
                    $queueTime = abs(strtotime($parentCall->c_created_dt) - strtotime($parentCall->c_queue_start_dt));
                    $callQueue = new CallLogQueue([
                        'clq_cl_id' => $log->cl_id,
                        'clq_is_transfer' => $parentCall->c_is_transfer,
                        'clq_queue_time' => $queueTime,
                        'clq_access_count' => (int)CallUserAccess::find()
                            ->andWhere(['cua_call_id' => $parentCall->c_id])
                            ->andWhere(['>=', 'cua_updated_dt', $queueTime])
                            ->andWhere(['<=', 'cua_created_dt', $parentCall->c_created_dt])
                            ->count()
                    ]);
                    if (!$callQueue->save()) {
                        throw new \RuntimeException(VarDumper::dumpAsString(['model' => $callQueue->toArray(), 'message' => $callQueue->getErrors()]));
                    }
                } else {
                    Yii::error(VarDumper::dumpAsString(['error' => 'Not found Parent Call', 'call' => $call->getAttributes()]), 'CallLogConferenceTransferService');
                }
            }

            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            \Yii::error(VarDumper::dumpAsString([
                'conference' => $conference->getAttributes(),
                'call' => $call->getAttributes(),
                'error' => $e->getMessage()
            ]), 'CallLogConferenceTransferService');
        }
    }
}
