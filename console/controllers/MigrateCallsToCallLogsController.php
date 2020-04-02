<?php

namespace console\controllers;

use common\models\Call;
use common\models\CallUserAccess;
use sales\model\callLog\entity\callLog\CallLog;
use sales\model\callLog\entity\callLogCase\CallLogCase;
use sales\model\callLog\entity\callLogLead\CallLogLead;
use sales\model\callLog\entity\callLogQueue\CallLogQueue;
use sales\model\callLog\entity\callLogRecord\CallLogRecord;
use sales\model\phoneList\entity\PhoneList;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\VarDumper;

class MigrateCallsToCallLogsController extends Controller
{
    private const DATE = '2019-11-01 00:00:00';
    public $limit;
    public $offset;
    public $dateFrom;
    public $dateTo;

    public function options($actionID)
    {
        if ($actionID === 'migrate-calls-to-call-log') {
            return array_merge(parent::options($actionID), [
                'limit', 'offset', 'dateFrom', 'dateTo'
            ]);
        }
        return parent::options($actionID);
    }

    public function actionTruncateCallLogs(): void
    {
        $db = \Yii::$app->db;
        $db->createCommand('SET FOREIGN_KEY_CHECKS=0;')->execute();
        $db->createCommand()->truncateTable('{{%call_log}}')->execute();
        $db->createCommand()->truncateTable('{{%call_log_lead}}')->execute();
        $db->createCommand()->truncateTable('{{%call_log_case}}')->execute();
        $db->createCommand()->truncateTable('{{%call_log_queue}}')->execute();
        $db->createCommand()->truncateTable('{{%call_log_record}}')->execute();
        $db->createCommand('SET FOREIGN_KEY_CHECKS=1;')->execute();
    }

    private function getQueryBefore($limit, $offset, $dateFrom, $dateTo)
    {
        $query = Call::find()
            ->orderBy(['c_id' => SORT_ASC])
            ->offset($offset)
            ->limit($limit)
            ->andWhere(['>=', 'c_created_dt', $dateFrom])
            ->andWhere(['<', 'c_created_dt', $dateTo])
            ->andWhere(['<', 'c_created_dt', self::DATE])
            ->andWhere(['c_call_status' => [Call::TW_STATUS_COMPLETED, Call::TW_STATUS_BUSY, Call::TW_STATUS_NO_ANSWER, Call::STATUS_FAILED, Call::TW_STATUS_CANCELED]])
            ->asArray();
        return $query;
    }

    private function getQueryAfter($limit, $offset, $dateFrom, $dateTo)
    {
        $query = Call::find()->alias('c')
            ->select('c.*')
            ->addSelect(
                'parent.c_id as `parent_c_id`,
                         parent.c_parent_id as `parent_c_parent_id`,
                         parent.c_created_dt as `parent_c_created_dt`,
                         parent.c_call_duration as `parent_c_call_duration`,
                         parent.c_call_type_id as `parent_c_call_type_id`,
                         parent.c_source_type_id as `parent_c_source_type_id`,
                         parent.c_status_id as `parent_c_status_id`'
            )
            ->addSelect(
                'prev_child.c_id as `prev_child_c_id`,
                         prev_child.c_parent_id as `prev_child_c_parent_id`,
                         prev_child.c_created_dt as `prev_child_c_created_dt`,
                         prev_child.c_call_duration as `prev_child_c_call_duration`,
                         prev_child.c_call_type_id as `prev_child_c_call_type_id`,
                         prev_child.c_source_type_id as `prev_child_c_source_type_id`'
            )
            ->addSelect(
                'next_child.c_id as `next_child_c_id`,
                         next_child.c_parent_id as `next_child_c_parent_id`,
                         next_child.c_created_dt as `next_child_c_created_dt`,
                         next_child.c_call_duration as `next_child_c_call_duration`,
                         next_child.c_call_type_id as `next_child_c_call_type_id`,
                         next_child.c_source_type_id as `next_child_c_source_type_id`'
            )
            ->addSelect(
                'last_child.c_id as `last_child_c_id`,
                         last_child.c_parent_id as `last_child_c_parent_id`,
                         last_child.c_created_dt as `last_child_c_created_dt`,
                         last_child.c_call_duration as `last_child_c_call_duration`,
                         last_child.c_call_type_id as `last_child_c_call_type_id`,
                         last_child.c_source_type_id as `last_child_c_source_type_id`'
            )
            ->addSelect(
                'first_child.c_id as `first_child_c_id`,
                         first_child.c_parent_id as `first_child_c_parent_id`,
                         first_child.c_created_dt as `first_child_c_created_dt`,
                         first_child.c_call_duration as `first_child_c_call_duration`,
                         first_child.c_call_type_id as `first_child_c_call_type_id`,
                         first_child.c_source_type_id as `first_child_c_source_type_id`'
            )
            ->addSelect(
                'first_same_child.c_id as `first_same_child_c_id`,
                         first_same_child.c_parent_id as `first_same_child_c_parent_id`,
                         first_same_child.c_created_dt as `first_same_child_c_created_dt`,
                         first_same_child.c_call_duration as `first_same_child_c_call_duration`,
                         first_same_child.c_call_type_id as `first_same_child_c_call_type_id`,
                         first_same_child.c_source_type_id as `first_same_child_c_source_type_id`'
            )
            ->leftJoin('(select * from `call`) `parent`', 'parent.c_id = c.c_parent_id')
            ->leftJoin('(select * from `call`) `prev_child`', '`prev_child`.`c_id` = (select max(c_id) from `call` where c_parent_id = c.c_parent_id and c_id < c.c_id)')
            ->leftJoin('(select * from `call`) `next_child`', '`next_child`.`c_id` = (select min(c_id) from `call` where c_parent_id = c.c_parent_id and c_id > c.c_id)')
            ->leftJoin('(select * from `call`) `last_child`', '`last_child`.`c_id` = (select max(c_id) from `call` where c_parent_id = c.c_id and c_id > c.c_id)')
            ->leftJoin('(select * from `call`) `first_child`', '`first_child`.`c_id` = (select min(c_id) from `call` where c_parent_id = c.c_id and c_id > c.c_id)')
            ->leftJoin('(select * from `call`) `first_same_child`', '`first_same_child`.`c_id` = (select min(c_id) from `call` where c_parent_id = c.c_parent_id)')
            ->orderBy(['c.c_id' => SORT_ASC])
            ->offset($offset)
            ->limit($limit)
            ->andWhere(['>=', 'c.c_created_dt', $dateFrom])
            ->andWhere(['<', 'c.c_created_dt', $dateTo])
            ->andWhere(['c.c_call_status' => [Call::TW_STATUS_COMPLETED, Call::TW_STATUS_BUSY, Call::TW_STATUS_NO_ANSWER, Call::TW_STATUS_FAILED, Call::TW_STATUS_CANCELED]])
            ->asArray();
        return $query;
    }

    public function actionMigrateCallsToCallLog($limit, $offset, $dateFrom, $dateTo): void
    {
        $limit = ($limit);
        $offset = ($offset);
        $dateFrom = strtotime($dateFrom);
        $dateTo = strtotime($dateTo);
        if ($dateTo <= $dateFrom) {
            echo 'arg dateTo must be greater then dateFrom' . PHP_EOL;
            return;
        }
        $dateTime = strtotime(self::DATE);
        if ($dateTo > $dateTime && $dateFrom < $dateTime) {
            echo 'after ' . self::DATE . ' dateTo, dateFrom must be greater then ' . self::DATE . PHP_EOL;
            return;
        }

        $dateFrom = date('Y-m-d H:i:s', $dateFrom);
        $dateTo = date('Y-m-d H:i:s', $dateTo);
        printf(PHP_EOL . '--- Start [' . date('Y-m-d H:i:s') . '] %s ---' . PHP_EOL, $this->ansiFormat(self::class . '\\' . $this->action->id, Console::FG_YELLOW));
        $time_start = microtime(true);
        printf(PHP_EOL . '--- Query settings: limit [' . $limit . '] offset [' . $offset . '] dateFrom [' . $dateFrom . '] dateTo [' . $dateTo . '] ---' . PHP_EOL, null);

        $logs = [];
        $n = 0;

        if (strtotime($dateFrom) < strtotime(self::DATE)) {
            $query = $this->getQueryBefore($limit, $offset, $dateFrom, $dateTo);
        } else {
            $query = $this->getQueryAfter($limit, $offset, $dateFrom, $dateTo);
        }

        $total = (clone $query)->count();

        Console::startProgress(0, $total, 'Counting objects: ', false);

        foreach ($query->batch() as $calls) {
            foreach ($calls as $call) {
                $this->processCall($call, $logs);
                $n++;
                Console::updateProgress($n, $total);
            }
        }

        Console::endProgress("done." . PHP_EOL);

        if ($logs) {
            foreach ($logs as $log) {
                print_r($log);
            }
            $file = 'err_calls_' . $limit . '_' . $offset . '_' . str_replace([':', ' ', '-'], '', $dateFrom) . '_' . str_replace([':', ' ', '-'], '', $dateTo) . '.txt';
            file_put_contents($file, VarDumper::dumpAsString($logs));
            echo ' --- Errors(' . count($logs) . ') ---' . PHP_EOL;
            echo 'log file: ' . $file . PHP_EOL;
        }

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);

        printf(PHP_EOL . 'Execute Time: %s' . PHP_EOL, $this->ansiFormat($time . ' s', Console::FG_RED));
        printf(PHP_EOL . ' --- End [' . date('Y-m-d H:i:s') . '] %s ---' . PHP_EOL . PHP_EOL, $this->ansiFormat(self::class . '\\' . $this->action->id, Console::FG_YELLOW));
    }

    private function processCall($call, &$log): void
    {
        if (CallLog::find()->andWhere(['cl_id' => $call['c_id']])->exists()) {
            $log[] = [
                'Call Id' => $call['c_id'],
                'Message' => ' is already exist',
            ];
            return;
        }

        if (strtotime($call['c_created_dt']) < strtotime(self::DATE)) {
            $this->createCallLogs($call, $log, [], []);
            return;
        }

        if ($call['c_call_type_id'] == Call::CALL_TYPE_OUT) {
            if ($call['c_parent_id']) {
                $this->processOutCall($call, $log);
                return;
            }
//            return;
        }

        if ($call['c_call_type_id'] == Call::CALL_TYPE_IN && $call['c_parent_id'] == null && $call['c_status_id'] != Call::STATUS_COMPLETED && $call['c_source_type_id'] != Call::SOURCE_TRANSFER_CALL) {
            $this->directInNotAcceptedCall($call,$log);
            return;
        }

        if ($call['c_call_type_id'] == Call::CALL_TYPE_IN && $call['c_parent_id'] == null && $call['c_status_id'] != Call::STATUS_COMPLETED && $call['c_source_type_id'] == Call::SOURCE_TRANSFER_CALL) {
            $this->transferInNotAcceptedCall($call,$log);
            return;
        }

        if ($call['c_call_type_id'] == Call::CALL_TYPE_IN && $call['c_parent_id'] != null && $call['c_source_type_id'] != Call::SOURCE_TRANSFER_CALL && $call['parent_c_call_type_id'] == Call::CALL_TYPE_IN) {
            $this->firstInAcceptedCall($call,$log);
            return;
        }

        if ($call['c_call_type_id'] == Call::CALL_TYPE_IN && $call['c_parent_id'] != null && $call['c_source_type_id'] == Call::SOURCE_TRANSFER_CALL && $call['parent_c_parent_id'] == null && $call['parent_c_call_type_id'] == Call::CALL_TYPE_IN) {
            $this->inTransferInAcceptedCall($call,$log);
            return;
        }

        $this->createCallLogs($call, $log, [], []);

    }

    private function inTransferInAcceptedCall($call, array &$log): void
    {
        $callData['cl_is_transfer'] = ($call['next_child_c_id'] != null) ? true : false;
        $callData['cl_category_id'] = $call['first_same_child_c_source_type_id'];

        $queueData['clq_queue_time'] = strtotime($call['c_created_dt']) - (strtotime($call['prev_child_c_created_dt']) + $call['prev_child_c_call_duration']);
        $queueData['clq_access_count'] = CallUserAccess::find()->andWhere(['cua_call_id' => $call['c_parent_id']])->andWhere(['>', 'cua_created_dt', $call['prev_child_c_created_dt']])->count();
        $queueData['clq_is_transfer'] = true;

        $this->createCallLogs(
            $call,
            $log,
            $callData,
            $queueData
        );
    }

    private function firstInAcceptedCall($call, array &$log): void
    {
        $callData['cl_call_finished_dt'] = date('Y-m-d H:i:s', (strtotime($call['c_created_dt']) + $call['c_call_duration']));
        $callData['cl_is_transfer'] = ($call['next_child_c_id'] != null) ? true : false;

        $queueData['clq_queue_time'] = strtotime($call['c_created_dt']) - strtotime($call['parent_c_created_dt']);
        $queueData['clq_access_count'] = CallUserAccess::find()->andWhere(['cua_call_id' => $call['c_parent_id']])->andWhere(['<=', 'cua_created_dt', $call['c_created_dt']])->count();

        $this->createCallLogs(
            $call,
            $log,
            $callData,
            $queueData
        );
    }

    private function transferInNotAcceptedCall($call, array &$log): void
    {
        $callData['cl_call_created_dt'] = date('Y-m-d H:i:s', (strtotime($call['last_child_c_created_dt']) + $call['last_child_c_call_duration']));
        $callData['cl_call_finished_dt'] = date('Y-m-d H:i:s', (strtotime($callData['cl_call_created_dt']) + $call['c_call_duration']));
        $callData['cl_duration'] = strtotime($callData['cl_call_finished_dt']) -  strtotime($callData['cl_call_created_dt']);
        $callData['cl_category_id'] = $call['first_child_c_source_type_id'];

        $queueData['clq_queue_time'] = $callData['cl_duration'];
        $queueData['clq_access_count'] = CallUserAccess::find()->andWhere(['cua_call_id' => $call['c_id']])->andWhere(['>=', 'cua_created_dt', $callData['cl_call_created_dt']])->count();
        $queueData['clq_is_transfer'] = true;

        $this->createCallLogs(
            $call,
            $log,
            $callData,
            $queueData
        );
    }

    private function directInNotAcceptedCall($call, array &$log): void
    {
        $this->createCallLogs(
            $call,
            $log,
            [],
            [
                'clq_queue_time' => $call['c_call_duration'],
                'clq_access_count' => CallUserAccess::find()->andWhere(['cua_call_id' => $call['c_id']])->count(),
            ]
        );
    }

    private function processOutCall($call, array &$log): void
    {
        $this->createCallLogs($call, $log, [], []);
    }

    private function createCallLogs($call, array &$log, array $callData = [], array $queueData = []): void
    {
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $callLog = new CallLog();
            $callLog->cl_id = $call['c_id'];
            $callLog->cl_call_sid = $call['c_call_sid'];
            $callLog->cl_type_id = $call['c_call_type_id'];
            $callLog->cl_phone_from = $call['c_from'];
            $callLog->cl_phone_to = $call['c_to'];
            $callLog->cl_duration = $callData['cl_duration'] ?? $call['c_call_duration'];
            $callLog->cl_user_id = $call['c_created_user_id'];
            $callLog->cl_call_created_dt = $callData['cl_call_created_dt'] ?? $call['c_created_dt'];
            $callLog->cl_call_finished_dt = $callData['cl_call_finished_dt'] ?? date('Y-m-d H:i:s', (strtotime($call['c_created_dt']) + $call['c_call_duration']));
            $callLog->cl_project_id = $call['c_project_id'];
            $callLog->cl_price = $call['c_price'];
            $callLog->cl_category_id = $callData['cl_category_id'] ?? (in_array($call['c_source_type_id'], [
                Call::SOURCE_GENERAL_LINE, Call::SOURCE_DIRECT_CALL, Call::SOURCE_REDIRECT_CALL, Call::SOURCE_TRANSFER_CALL, Call::SOURCE_CONFERENCE_CALL, Call::SOURCE_REDIAL_CALL
            ], false) ? $call['c_source_type_id'] : null);
            $callLog->cl_is_transfer = $callData['cl_is_transfer'] ?? false;
            $callLog->cl_department_id = $call['c_dep_id'];
            $callLog->cl_client_id = $call['c_client_id'];
            $callLog->cl_parent_id = $call['c_parent_id'];
            $callLog->cl_status_id = $call['c_status_id'] ?: self::convertStatusFromTwStatus($call['c_call_status']);

            if ($call['c_from'] && $call['c_call_type_id'] == Call::CALL_TYPE_OUT) {
                if ($phoneList = PhoneList::find()->select(['pl_id'])->andWhere(['pl_phone_number' => $call['c_from']])->asArray()->one()) {
                    $callLog->cl_phone_list_id = $phoneList['pl_id'];
                }
            } elseif ($call['c_to'] && $call['c_call_type_id'] == Call::CALL_TYPE_IN) {
                if ($phoneList = PhoneList::find()->select(['pl_id'])->andWhere(['pl_phone_number' => $call['c_to']])->asArray()->one()) {
                    $callLog->cl_phone_list_id = $phoneList['pl_id'];
                }
            }

            if (!$callLog->save()) {
                throw new \RuntimeException(VarDumper::dumpAsString(['model' => $callLog->toArray(), 'message' => $callLog->getErrors()]));
            }

            if ($call['c_lead_id']) {
                $callLogLead = new CallLogLead([
                    'cll_cl_id' => $callLog->cl_id,
                    'cll_lead_id' => $call['c_lead_id']
                ]);
                if (!$callLogLead->save()) {
                    throw new \RuntimeException(VarDumper::dumpAsString(['model' => $callLogLead->toArray(), 'message' => $callLogLead->getErrors()]));
                }
            }

            if ($call['c_case_id']) {
                $callLogCase = new CallLogCase([
                    'clc_cl_id' => $callLog->cl_id,
                    'clc_case_id' => $call['c_case_id']
                ]);
                if (!$callLogCase->save()) {
                    throw new \RuntimeException(VarDumper::dumpAsString(['model' => $callLogCase->toArray(), 'message' => $callLogCase->getErrors()]));
                }
            }

            if ($call['c_recording_duration'] || $call['c_recording_sid']) {
                $callLogRecord = new CallLogRecord([
                    'clr_cl_id' => $callLog->cl_id,
                    'clr_duration' => $call['c_recording_duration'],
                    'clr_record_sid' => $call['c_recording_sid'],
                ]);
                if (!$callLogRecord->save()) {
                    throw new \RuntimeException(VarDumper::dumpAsString(['model' => $callLogRecord->toArray(), 'message' => $callLogRecord->getErrors()]));
                }
            }

            if ($queueData) {
                $callQueue = new CallLogQueue([
                    'clq_cl_id' => $callLog->cl_id,
                    'clq_access_count' => $queueData['clq_access_count'] ?? null,
                    'clq_queue_time' => $queueData['clq_queue_time'] ?? null,
                    'clq_is_transfer' => $queueData['clq_is_transfer'] ?? null,
                ]);
                if (!$callQueue->save()) {
                    throw new \RuntimeException(VarDumper::dumpAsString(['model' => $callQueue->toArray(), 'message' => $callQueue->getErrors()]));
                }
            }

            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $log[] = [
                'Call Id' => $call['c_id'],
                'Error' => $e->getMessage(),
            ];
        }
    }

    private static function convertStatusFromTwStatus(?string $twStatus): ?int
    {
        switch ($twStatus) {
            case 'busy':
                return Call::STATUS_BUSY;
            case 'canceled':
                return Call::STATUS_CANCELED;
            case 'completed':
                return Call::STATUS_COMPLETED;
            case 'failed':
                return Call::STATUS_FAILED;
            case 'no-answer':
                return Call::STATUS_NO_ANSWER;
        }
        return Call::STATUS_CANCELED;
    }
}
