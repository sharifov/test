<?php

namespace src\model\callLog\services;

use common\models\Call;
use common\models\CallUserAccess;
use common\models\Client;
use common\models\Employee;
use common\models\Lead;
use common\models\Notifications;
use common\models\PhoneBlacklist;
use common\models\UserParams;
use src\entities\cases\Cases;
use src\guards\phone\PhoneBlackListGuard;
use src\helpers\call\CallHelper;
use src\model\callLog\entity\callLog\CallLog;
use src\model\callLog\entity\callLog\CallLogStatus;
use src\model\callLog\entity\callLog\CallLogType;
use src\model\callLog\entity\callLogCase\CallLogCase;
use src\model\callLog\entity\callLogLead\CallLogLead;
use src\model\callLog\entity\callLogQueue\CallLogQueue;
use src\model\callLog\entity\callLogRecord\CallLogRecord;
use src\model\callLog\entity\callLogUserAccess\CallLogUserAccess;
use src\model\callLogFilterGuard\entity\CallLogFilterGuard;
use src\model\callLogFilterGuard\repository\CallLogFilterGuardRepository;
use src\model\callLogFilterGuard\service\CallLogFilterGuardService;
use src\model\callNote\entity\CallNote;
use src\model\leadData\entity\LeadData;
use src\model\leadDataKey\services\LeadDataKeyDictionary;
use src\model\phoneList\entity\PhoneList;
use src\model\voip\phoneDevice\device\VoipDevice;
use Yii;
use yii\db\Expression;
use yii\helpers\VarDumper;

/**
 * Class CallLogTransferService
 *
 * @property array $call
 * @property array $callLog
 * @property array $queue
 * @property array $callUserAccess
 */
class CallLogTransferService
{
    private $call = [];
    private $callLog = [];
    private $queue = [];
    private $callUserAccess = [];

    public function saveRecord(Call $call): void
    {
        if (!$call->c_recording_sid) {
            return;
        }

        if (
            !$call->isGeneralParent()
            && (
                (($call->c_group_id == null || ($call->isTransfer() && $call->isSourceTransfer())) && $call->isOut())
                || ($call->isIn() && (strtotime($call->c_queue_start_dt) > strtotime($call->c_created_dt)))
            )
        ) {
            $clr_cl_id = $call->c_parent_id;
        } else {
            $clr_cl_id = $call->c_id;
        }

        $callLogRecord = new CallLogRecord([
            'clr_cl_id' => $clr_cl_id,
            'clr_duration' => $call->c_recording_duration,
            'clr_record_sid' => $call->c_recording_sid,
        ]);

        try {
            $callLogRecord->save(false);
        } catch (\Throwable $e) {
            Yii::error(VarDumper::dumpAsString([
                'category' => 'saveRecord',
                'error' => $e->getMessage(),
                'model' => $callLogRecord->toArray(),
            ]), 'CallLogTransferService');
        }
    }

    public function transfer(Call $call): void
    {
        $this->call = $call->getAttributes();
        $this->call['phone_list_id'] = $call->getDataPhoneListId();

        $debugEnable = Yii::$app->params['settings']['call_log_debug_enable'] ?? false;
        if ($debugEnable) {
            Yii::info($this->call, 'info\DebugCallLog');
        }

        if ($call->isOut() && $call->isGeneralParent() && !$call->isTransfer()) {
            $this->outParentCall();
            return;
        }

        if ($call->isOut() && $call->isGeneralParent() && $call->isTransfer()) {
            $this->outTransferParentCall();
            return;
        }

        if (
            $call->isOut()
            && !$call->isGeneralParent()
            && (
                $call->c_group_id == null || ($call->isTransfer() && $call->isSourceTransfer())
            )
        ) {
            $this->outChildCall();
            return;
        }

        if (
            $call->isOut() &&
            !$call->isGeneralParent() &&
            $call->c_group_id != null &&
            (
                !$call->isTransfer() ||
                ($call->isTransfer() &&
                !$call->isSourceTransfer())
            )
        ) {
            $this->outChildTransferCall();
            return;
        }

        if (
            $call->isIn()
            && $call->isStatusCompleted()
            && (
                $call->isGeneralParent()
                || (!$call->isGeneralParent() && (strtotime($call->c_queue_start_dt) > strtotime($call->c_created_dt)))
            )
        ) {
            // In Accepted Parent Call
            return;
        }

        if ($call->isIn() && $call->isGeneralParent() && !$call->isStatusCompleted() && !$call->isTransfer()) {
            $this->directInNotAcceptedParentCall();
            return;
        }

        if (
            $call->isIn()
            && !$call->isStatusCompleted()
            && $call->isTransfer()
            && (
                $call->isGeneralParent()
                || (!$call->isGeneralParent() && (strtotime($call->c_queue_start_dt) > strtotime($call->c_created_dt)))
            )
        ) {
            $this->transferInNotAcceptedParentCall();
            return;
        }

        if ($call->isIn() && !$call->isGeneralParent() && $call->c_group_id == null && !$call->isTransfer()) {
            $this->simpleInAcceptedChildCall();
            return;
        }

        if (
            $call->isIn()
            && !$call->isGeneralParent()
            && $call->c_group_id != null
            && (strtotime($call->c_queue_start_dt) <= strtotime($call->c_created_dt))
        ) {
            $this->transferInAcceptedChildCall();
            return;
        }

        if (
            $call->isIn()
            && !$call->isGeneralParent()
            && !$call->isTransfer()
            && !$call->isStatusCompleted()
            && (strtotime($call->c_queue_start_dt) > strtotime($call->c_created_dt))
            && $call->c_group_id != null
        ) {
            $this->exceptionCall();
            return;
        }

        Yii::info(VarDumper::dumpAsString(['message' => 'Call Id: ' . $call->c_id . ' not found rule for transfer Call to Call Log', 'Call' => $this->call]), 'info\CallLogTransferService');
        $this->createCallLogs();
    }

    private function exceptionCall(): void
    {
        Yii::error(VarDumper::dumpAsString(['category' => 'exceptionCall', 'Call' => $this->call]), 'CallLogTransferService');
        $this->createCallLogs();
    }

    private function outParentCall(): void
    {
        $this->callLog['cl_group_id'] = $this->call['c_id'];

        $firstChild = Call::find()->select(['c_status_id'])->andWhere(['c_parent_id' => $this->call['c_id']])->orderBy(['c_id' => SORT_ASC])->asArray()->limit(1)->one();
        if ($this->call['c_status_id'] == Call::STATUS_COMPLETED && $firstChild && array_key_exists((int)$firstChild['c_status_id'], CallLogStatus::getList())) {
            $this->callLog['cl_status_id'] = $firstChild['c_status_id'];
        } else {
            $this->callLog['cl_status_id'] = $this->call['c_status_id'];
        }

        $this->createCallLogs();
    }

    private function outChildTransferCall(): void
    {
        $this->createCallLogs();
    }

    private function transferInAcceptedChildCall(): void
    {
//        if ($this->call['c_status_id'] == Call::STATUS_NO_ANSWER && $this->call['c_source_type_id'] != Call::SOURCE_TRANSFER_CALL) {
//            $this->callLog['cl_status_id'] = CallLogStatus::FAILED;
//        } else {
            $this->callLog['cl_status_id'] = $this->call['c_status_id'];
//        }

//        if ($this->call['c_queue_start_dt'] !== null) {
//            $this->callLog['cl_duration'] = $this->call['c_call_duration'] + (strtotime($this->call['c_created_dt']) - strtotime($this->call['c_queue_start_dt']));
//        } else {
            $this->callLog['cl_duration'] = $this->call['c_call_duration'];
//        }

        if ($this->call['c_queue_start_dt'] !== null) {
            $this->queue['clq_queue_time'] = strtotime($this->call['c_created_dt']) - strtotime($this->call['c_queue_start_dt']);
        } else {
            $this->queue['clq_queue_time'] = null;
        }

        $this->callUserAccess = CallUserAccess::find()
            ->andWhere(['cua_call_id' => $this->call['c_parent_id']])
            ->andWhere(['>=', 'cua_updated_dt', $this->call['c_queue_start_dt']])
            ->andWhere(['<=', 'cua_created_dt', $this->call['c_created_dt']])
            ->asArray()
            ->all();

        $this->queue['clq_access_count'] = count($this->callUserAccess);

//        $this->queue['clq_is_transfer'] = ($this->call['c_group_id'] != $this->call['c_id']) ? true : false;

        $this->createCallLogs();
    }

    private function simpleInAcceptedChildCall(): void
    {
//        if ($this->call['c_status_id'] == Call::STATUS_NO_ANSWER && $this->call['c_source_type_id'] != Call::SOURCE_TRANSFER_CALL) {
//            $this->callLog['cl_status_id'] = CallLogStatus::FAILED;
//        } else {
            $this->callLog['cl_status_id'] = $this->call['c_status_id'];
//        }

//        if ($this->call['c_queue_start_dt'] !== null) {
//            $this->callLog['cl_duration'] = $this->call['c_call_duration'] + (strtotime($this->call['c_created_dt']) - strtotime($this->call['c_queue_start_dt']));
//        } else {
            $this->callLog['cl_duration'] = $this->call['c_call_duration'];
//        }

        $this->callLog['cl_group_id'] = $this->call['c_id'];

        if ($this->call['c_queue_start_dt'] !== null) {
            $this->queue['clq_queue_time'] = strtotime($this->call['c_created_dt']) - strtotime($this->call['c_queue_start_dt']);
        } else {
            $this->queue['clq_queue_time'] = null;
        }

        $this->callUserAccess = CallUserAccess::find()
            ->andWhere(['cua_call_id' => $this->call['c_parent_id']])
            ->andWhere(['<=', 'cua_created_dt', $this->call['c_created_dt']])
            ->asArray()
            ->all();

        $this->queue['clq_access_count'] = count($this->callUserAccess);

        $this->createCallLogs();
    }

    private function transferInNotAcceptedParentCall(): void
    {
        $this->callLog['cl_call_created_dt'] = $this->call['c_queue_start_dt'];
        $this->callLog['cl_call_finished_dt'] = date('Y-m-d H:i:s', (strtotime($this->call['c_created_dt']) + $this->call['c_call_duration']));
        $this->callLog['cl_duration'] = strtotime($this->callLog['cl_call_finished_dt']) - strtotime($this->callLog['cl_call_created_dt']);
        $this->callLog['cl_is_transfer'] = false;

        $this->queue['clq_queue_time'] = $this->callLog['cl_duration'];
        $this->callUserAccess = CallUserAccess::find()
            ->andWhere(['cua_call_id' => $this->call['c_id']])
            ->andWhere(['>=', 'cua_created_dt', $this->callLog['cl_call_created_dt']])
            ->asArray()
            ->all();
        $this->queue['clq_access_count'] = count($this->callUserAccess);

        $this->queue['clq_is_transfer'] = true;

        $this->createCallLogs();
    }

    private function directInNotAcceptedParentCall(): void
    {
        $this->callLog['cl_group_id'] = $this->call['c_id'];

        $this->queue['clq_queue_time'] = $this->call['c_call_duration'];
        $this->callUserAccess = CallUserAccess::find()
            ->andWhere(['cua_call_id' => $this->call['c_id']])
            ->asArray()
            ->all();
        $this->queue['clq_access_count'] = count($this->callUserAccess);

        $this->createCallLogs();
    }

    private function outChildCall(): void
    {
        if (!$this->call['c_is_transfer'] && $this->call['c_status_id'] != Call::STATUS_COMPLETED) {
            try {
                CallLog::updateAll(['cl_status_id' => $this->call['c_status_id']], 'cl_id = ' . (int)$this->call['c_parent_id']);
            } catch (\Throwable $e) {
                Yii::error(VarDumper::dumpAsString(['category' => 'outChildCall', 'error' => $e->getMessage()]), 'CallLogTransferService');
            }
        }
    }

    private function outTransferParentCall(): void
    {
        $this->createCallLogs();
    }

    private function createCallLogs(): void
    {
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $log = new CallLog();
            $log->cl_id = $this->call['c_id'];
            $log->cl_call_sid = $this->map('cl_call_sid', 'c_call_sid');
            if ($log->cl_type_id = $this->map('cl_type_id', 'c_call_type_id')) {
                $log->cl_type_id = (int)$log->cl_type_id;
            }
            $log->cl_phone_from = $this->map('cl_phone_from', 'c_from');
            $log->cl_phone_to = $this->map('cl_phone_to', 'c_to');
            $log->cl_duration = $this->map('cl_duration', 'c_call_duration');
            if ($log->cl_duration < 0) {
                $log->cl_duration = 0;
            }
            $log->cl_user_id = $this->map('cl_user_id', 'c_created_user_id');
            $log->cl_call_created_dt = $this->map('cl_call_created_dt', 'c_created_dt');
            $log->cl_call_finished_dt = (array_key_exists('cl_call_finished_dt', $this->callLog))
                ? $this->callLog['cl_call_finished_dt']
                : date('Y-m-d H:i:s', (strtotime($this->call['c_created_dt']) + $this->call['c_call_duration']));
            $log->cl_project_id = $this->map('cl_project_id', 'c_project_id');
            $log->cl_price = $this->map('cl_price', 'c_price');
            if ((array_key_exists('cl_category_id', $this->callLog))) {
                $log->cl_category_id = $this->callLog['cl_category_id'];
            } else {
                $log->cl_category_id = $this->call['c_source_type_id'];
            }
            if (array_key_exists('cl_is_transfer', $this->callLog)) {
                $log->cl_is_transfer = $this->callLog['cl_is_transfer'];
            } elseif ($this->call['c_is_transfer']) {
                $log->cl_is_transfer = $this->call['c_is_transfer'];
            } else {
                $log->cl_is_transfer = false;
            }
            $log->cl_department_id = $this->map('cl_department_id', 'c_dep_id');
            $log->cl_client_id = $this->map('cl_client_id', 'c_client_id');
            if (!$log->cl_client_id) {
                if ($this->call['c_lead_id']) {
                    if ($lead = Lead::find()->select(['client_id'])->andWhere(['id' => $this->call['c_lead_id']])->asArray()->one()) {
                        $log->cl_client_id = $lead['client_id'];
                    }
                } elseif ($this->call['c_case_id']) {
                    if ($case = Cases::find()->select(['cs_client_id'])->andWhere(['cs_id' => $this->call['c_case_id']])->asArray()->one()) {
                        $log->cl_client_id = $case['cs_client_id'];
                    }
                }
            }
            $log->cl_group_id = $this->map('cl_group_id', 'c_group_id');
            $log->cl_status_id = $this->map('cl_status_id', 'c_status_id');
            $log->cl_stir_status = $this->map('cl_stir_status', 'c_stir_status');

            if (!$log->cl_stir_status && $childStirStatus = self::getChildStirStatus((int) $log->cl_id)) {
                $log->cl_stir_status = $childStirStatus;
            }

            /*
            if ($this->call['c_from'] && $this->call['c_call_type_id'] == Call::CALL_TYPE_OUT) {
                if ($phoneList = PhoneList::find()->select(['pl_id'])->andWhere(['pl_phone_number' => $this->call['c_from']])->asArray()->one()) {
                    $log->cl_phone_list_id = $phoneList['pl_id'];
                }
            } elseif ($this->call['c_to'] && $this->call['c_call_type_id'] == Call::CALL_TYPE_IN) {
                if ($phoneList = PhoneList::find()->select(['pl_id'])->andWhere(['pl_phone_number' => $this->call['c_to']])->asArray()->one()) {
                    $log->cl_phone_list_id = $phoneList['pl_id'];
                }
            }
            */
            $log->cl_phone_list_id = $this->call['phone_list_id'];


            if ($log->cl_call_created_dt) {
                $time = strtotime($log->cl_call_created_dt);
                $log->cl_year = date('Y', $time);
                $log->cl_month = date('m', $time);
            }

            $log->cl_conference_id = $this->call['c_conference_id'];

            if (!$log->save()) {
                throw new \RuntimeException(VarDumper::dumpAsString(['model' => $log->toArray(), 'message' => $log->getErrors()]));
            }

            if (($parentId = $this->call['c_parent_id'] ?? null) && $callLogFilterGuard = CallLogFilterGuardService::checkCallLog($parentId)) {
                $callLogFilterGuard->clfg_call_log_id = $log->cl_id;
                (new CallLogFilterGuardRepository($callLogFilterGuard))->save();
            }

            if ($this->call['c_lead_id']) {
                CallLogLeadCreateService::create($log->cl_id, $this->call['c_lead_id']);
            }
            if ($this->call['c_parent_id'] && $this->call['c_id'] !== $log->cl_group_id) {
                try {
                    LeadData::updateAll(['ld_field_value' => $this->call['c_id']], ['ld_field_key' => LeadDataKeyDictionary::KEY_CREATED_BY_CALL_ID, 'ld_field_value' => $this->call['c_parent_id']]);
                } catch (\Throwable $e) {
                    Yii::error([
                        'message' => $e->getMessage(),
                        'callId' => $this->call['c_id'],
                        'parentCallId' => $this->call['c_parent_id'],
                    ], 'CallLogTransferService');
                }
            }

            if ($this->call['c_case_id']) {
                $callLogCase = new CallLogCase([
                    'clc_cl_id' => $log->cl_id,
                    'clc_case_id' => $this->call['c_case_id']
                ]);
                if (!$callLogCase->save()) {
                    throw new \RuntimeException(VarDumper::dumpAsString(['model' => $callLogCase->toArray(), 'message' => $callLogCase->getErrors()]));
                }
            }

            if ($this->queue) {
                if (array_key_exists('clq_queue_time', $this->queue)) {
                    $this->queue['clq_queue_time'] = abs($this->queue['clq_queue_time']);
                }
                $callQueue = new CallLogQueue([
                    'clq_cl_id' => $log->cl_id,
                    'clq_access_count' => $this->queue['clq_access_count'] ?? null,
                    'clq_queue_time' => $this->queue['clq_queue_time'] ?? null,
                    'clq_is_transfer' => $this->queue['clq_is_transfer'] ?? null,
                ]);
                if (!$callQueue->save()) {
                    throw new \RuntimeException(VarDumper::dumpAsString(['model' => $callQueue->toArray(), 'message' => $callQueue->getErrors()]));
                }
            }

            if ($this->callUserAccess) {
                foreach ($this->callUserAccess as $userAccess) {
                    $callLogUserAccess = CallLogUserAccess::create(
                        $log->cl_id,
                        $userAccess['cua_user_id'],
                        $userAccess['cua_status_id'],
                        $userAccess['cua_created_dt'],
                        $userAccess['cua_updated_dt']
                    );
                    if (!$callLogUserAccess->save(false)) {
                        throw new \RuntimeException(VarDumper::dumpAsString(['model' => $callLogUserAccess->toArray()]));
                    }
                }
            }

            $transaction->commit();

            if ($log->cl_user_id && ($log->isIn() || $log->isOut())) {
                $this->sendHistorySocketMessage($log->getAttributes(), $this->call['c_case_id'], $this->call['c_lead_id']);
            }
        } catch (\Throwable $e) {
            $transaction->rollBack();
            \Yii::error(VarDumper::dumpAsString(['category' => 'createCallLogs', 'Call' => $this->call, 'error' => $e->getMessage()]), 'CallLogTransferService');
        }
    }

    private function sendHistorySocketMessage(array $call, ?int $caseId, ?int $leadId): void
    {
        $toUserId = (int)$call['cl_user_id'];
        if (!$toUserId) {
            return;
        }
        $toUser = Employee::find()->select(['id', 'up_timezone'])->andWhere(['id' => $toUserId])->leftJoin(UserParams::tableName(), 'up_user_id = id')->asArray()->one();
        if (!$toUser) {
            return;
        }

        $call['case_id'] = $caseId;
        $call['lead_id'] = $leadId;

        $callNote  = CallNote::find()->select(['cn_note'])->andWhere(['cn_call_id' => $call['cl_id']])->orderBy(['cn_created_dt' => SORT_DESC])->asArray()->limit(1)->one();
        $call['callNote'] = $callNote['cn_note'] ?? '';

        $call['user_id'] = null;
        if ((int)$call['cl_type_id'] === Call::CALL_TYPE_OUT) {
            if (VoipDevice::isValid($call['cl_phone_to'])) {
                $call['user_id'] = VoipDevice::getUserId($call['cl_phone_to']);
            }
            $isPhoneInBlackList = PhoneBlacklist::find()->andWhere(['pbl_phone' => $call['cl_phone_to'], 'pbl_enabled' => true])->exists();
            $call['phone_to_blacklist'] = $call['cl_phone_to'];
        } else {
            if (VoipDevice::isValid($call['cl_phone_from'])) {
                $call['user_id'] = VoipDevice::getUserId($call['cl_phone_from']);
            }
            $isPhoneInBlackList = PhoneBlacklist::find()->andWhere(['pbl_phone' => $call['cl_phone_from'], 'pbl_enabled' => true])->exists();
            $call['phone_to_blacklist'] = $call['cl_phone_from'];
        }
        $call['in_blacklist'] = $isPhoneInBlackList;
        $call['nickname'] = null;
        if ($call['user_id']) {
            if ($user = Employee::find()->select(['nickname'])->andWhere(['id' => $call['user_id']])->asArray()->one()) {
                $call['nickname'] = $user['nickname'];
            }
        }

        $call['client_name'] = null;
        if ($call['cl_client_id']) {
            $client = Client::find()
                ->addSelect(new Expression("if (is_company = 1, company_name, if (first_name is not null, if (last_name is not null, concat(first_name, ' ', last_name), first_name), null)) as client_name"))
                ->andWhere(['id' => $call['cl_client_id']])
                ->asArray()
                ->one();
            if ($client) {
                $call['client_name'] = $client['client_name'];
            }
        }

        $call['formatted'] = null;
        if ($call['client_name']) {
            $call['formatted'] = $call['client_name'];
        } else {
            if ((int)$call['cl_type_id'] === Call::CALL_TYPE_OUT) {
                if ($call['nickname']) {
                    $call['formatted'] = $call['nickname'];
                } else {
                    $call['formatted'] = $call['cl_phone_to'];
                }
            } else {
                if ($call['nickname']) {
                    $call['formatted'] = $call['nickname'];
                } else {
                    $call['formatted'] = $call['cl_phone_from'];
                }
            }
        }

        $call['cl_call_created_dt'] = Employee::convertTimeFromUtcToUserTime($toUser['up_timezone'], strtotime($call['cl_call_created_dt']), 'h:i A');

        Notifications::publish(
            'addCallToHistory',
            ['user_id' => (int)$call['cl_user_id']],
            ['data' => [
                'command' => 'addCallToHistory',
                'call' => CallHelper::formCallToHistoryTab($call, PhoneBlackListGuard::canAdd($toUserId))]
            ]
        );
    }

    private function map(string $callLogAttr, string $callAttr): ?string
    {
        return (array_key_exists($callLogAttr, $this->callLog)) ? $this->callLog[$callLogAttr] : $this->call[$callAttr];
    }

    /**
     * @param int $parentId
     * @return false|string
     */
    public static function getChildStirStatus(int $parentId)
    {
        return Call::find()
            ->select('c_stir_status')
            ->where(['c_parent_id' => $parentId])
            ->andWhere([
                'AND',
                    ['IS NOT', 'c_stir_status', null],
                    ['!=', 'c_stir_status', '']
                ])
            ->orderBy(['c_id' => SORT_DESC])
            ->limit(1)
            ->scalar();
    }
}
