<?php

namespace console\controllers;

use common\models\Call;
use common\models\CallUserAccess;
use common\models\Lead;
use sales\entities\cases\Cases;
use sales\model\callLog\entity\callLog\CallLog;
use sales\model\callLog\entity\callLog\CallLogCategory;
use sales\model\callLog\entity\callLog\CallLogStatus;
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
                         parent.c_status_id as `parent_c_status_id`,
                         parent.c_created_user_id as `parent_c_created_user_id`'
            )
            ->addSelect(
                'prev_child.c_id as `prev_child_c_id`,
                         prev_child.c_parent_id as `prev_child_c_parent_id`,
                         prev_child.c_created_dt as `prev_child_c_created_dt`,
                         prev_child.c_call_duration as `prev_child_c_call_duration`,
                         prev_child.c_call_type_id as `prev_child_c_call_type_id`,
                         prev_child.c_source_type_id as `prev_child_c_source_type_id`,
                         prev_child.c_created_user_id as `prev_child_c_created_user_id`'
            )
            ->addSelect(
                'next_child.c_id as `next_child_c_id`,
                         next_child.c_parent_id as `next_child_c_parent_id`,
                         next_child.c_created_dt as `next_child_c_created_dt`,
                         next_child.c_call_duration as `next_child_c_call_duration`,
                         next_child.c_call_type_id as `next_child_c_call_type_id`,
                         next_child.c_source_type_id as `next_child_c_source_type_id`,
                         next_child.c_created_user_id as `next_child_c_created_user_id`'
            )
            ->addSelect(
                'last_child.c_id as `last_child_c_id`,
                         last_child.c_parent_id as `last_child_c_parent_id`,
                         last_child.c_created_dt as `last_child_c_created_dt`,
                         last_child.c_call_duration as `last_child_c_call_duration`,
                         last_child.c_call_type_id as `last_child_c_call_type_id`,
                         last_child.c_source_type_id as `last_child_c_source_type_id`,
                         last_child.c_created_user_id as `last_child_c_created_user_id`'
            )
            ->addSelect(
                'first_child.c_id as `first_child_c_id`,
                         first_child.c_parent_id as `first_child_c_parent_id`,
                         first_child.c_created_dt as `first_child_c_created_dt`,
                         first_child.c_call_duration as `first_child_c_call_duration`,
                         first_child.c_call_type_id as `first_child_c_call_type_id`,
                         first_child.c_source_type_id as `first_child_c_source_type_id`,
                         first_child.c_recording_sid as `first_child_c_recording_sid`,
                         first_child.c_recording_duration as `first_child_c_recording_duration`,
                         first_child.c_created_user_id as `first_child_c_created_user_id`'
            )
            ->addSelect(
                'first_same_child.c_id as `first_same_child_c_id`,
                         first_same_child.c_parent_id as `first_same_child_c_parent_id`,
                         first_same_child.c_created_dt as `first_same_child_c_created_dt`,
                         first_same_child.c_call_duration as `first_same_child_c_call_duration`,
                         first_same_child.c_call_type_id as `first_same_child_c_call_type_id`,
                         first_same_child.c_source_type_id as `first_same_child_c_source_type_id`,
                         first_same_child.c_created_user_id as `first_same_child_c_created_user_id`'
            )
            ->addSelect(
                'last_same_child.c_id as `last_same_child_c_id`,
                         last_same_child.c_parent_id as `last_same_child_c_parent_id`,
                         last_same_child.c_created_dt as `last_same_child_c_created_dt`,
                         last_same_child.c_call_duration as `last_same_child_c_call_duration`,
                         last_same_child.c_call_type_id as `last_same_child_c_call_type_id`,
                         last_same_child.c_source_type_id as `last_same_child_c_source_type_id`,
                         last_same_child.c_created_user_id as `last_same_child_c_created_user_id`'
            )
            ->addSelect(
                'top_parent.c_id as `top_parent_c_id`,
                         top_parent.c_created_dt as `top_parent_c_created_dt`,
                         top_parent.c_call_duration as `top_parent_c_call_duration`'
            )
            ->addSelect(
                'bottom_child.c_id as `bottom_child_c_id`,
                         bottom_child.c_created_dt as `bottom_child_c_created_dt`,
                         bottom_child.c_call_duration as `bottom_child_c_call_duration`,
                         bottom_child.c_created_user_id as `bottom_child_c_created_user`'
            )
            ->leftJoin('(select * from `call`) `parent`', 'parent.c_id = c.c_parent_id')
            ->leftJoin('(select * from `call`) `prev_child`', '`prev_child`.`c_id` = (select max(c_id) from `call` where c_parent_id = c.c_parent_id and c_id < c.c_id)')
            ->leftJoin('(select * from `call`) `next_child`', '`next_child`.`c_id` = (select min(c_id) from `call` where c_parent_id = c.c_parent_id and c_id > c.c_id)')
            ->leftJoin('(select * from `call`) `last_child`', '`last_child`.`c_id` = (select max(c_id) from `call` where c_parent_id = c.c_id and c_id > c.c_id)')
            ->leftJoin('(select * from `call`) `first_child`', '`first_child`.`c_id` = (select min(c_id) from `call` where c_parent_id = c.c_id and c_id > c.c_id)')
            ->leftJoin('(select * from `call`) `first_same_child`', '`first_same_child`.`c_id` = (select min(c_id) from `call` where c_parent_id = c.c_parent_id)')
            ->leftJoin('(select * from `call`) `last_same_child`', '`last_same_child`.`c_id` = (select max(c_id) from `call` where c_parent_id = c.c_parent_id)')
            ->leftJoin('(select * from `call`) `top_parent`', '`top_parent`.`c_id` = parent.c_parent_id')
            ->leftJoin('(select * from `call`) `bottom_child`', '`bottom_child`.`c_id` = (select max(c_id) from `call` where c_parent_id = first_child.c_id)')
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
        if ($total >= $limit) {
            $total = $limit;
        }

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
            file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR  . 'runtime' . DIRECTORY_SEPARATOR . $file, VarDumper::dumpAsString($logs));
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

        if (
            $call['c_call_type_id'] == Call::CALL_TYPE_OUT
            && $call['c_parent_id'] == null
            && $call['first_child_c_id'] != null
            && $call['bottom_child_c_id'] == null
        ) {
            $this->outParentCalls($call, $log);
            return;
        }

        if (
            $call['c_call_type_id'] == Call::CALL_TYPE_OUT
            && $call['c_parent_id'] == null
            && $call['first_child_c_id'] != null
            && $call['bottom_child_c_id'] != null
        ) {
            $this->outTransferParentCalls($call, $log);
            return;
        }

        if (
            $call['c_call_type_id'] == Call::CALL_TYPE_OUT
            && $call['c_parent_id'] != null
            && $call['parent_c_parent_id'] == null
        ) {
            //$this->outChildCalls($call, $log);
            return;
        }

        if (
            $call['c_call_type_id'] == Call::CALL_TYPE_OUT
            && $call['c_parent_id'] != null
            && $call['first_child_c_id'] != null
        ) {
//            $this->outTransferPrimaryChildCall($call, $log);
            return;
        }

        if (
            $call['c_call_type_id'] == Call::CALL_TYPE_IN
            && $call['c_parent_id'] == null
            && $call['c_status_id'] == Call::STATUS_COMPLETED
            && (
                $call['last_child_c_id'] == null
                || $call['c_created_user_id'] == $call['last_child_c_created_user_id']
                || $call['last_child_c_created_user_id'] == null
            )
        ) {
            return;
        }

        if ($call['c_call_type_id'] == Call::CALL_TYPE_IN && $call['c_parent_id'] == null && $call['c_status_id'] != Call::STATUS_COMPLETED && $call['last_child_c_id'] == null) {
            $this->directInNotAcceptedParentCall($call, $log);
            return;
        }

        if (
            $call['c_call_type_id'] == Call::CALL_TYPE_IN
            && $call['c_parent_id'] == null
            && $call['last_child_c_id'] != null
            && (
                $call['c_status_id'] != Call::STATUS_COMPLETED
                || (
                    ($call['c_created_user_id'] == null || ($call['c_created_user_id'] != $call['last_child_c_created_user_id']))
                    && $call['last_child_c_created_user_id'] != null
                )
            )
        ) {
            $this->transferInNotAcceptedParentCall($call, $log);
            return;
        }

        if (
            $call['c_call_type_id'] == Call::CALL_TYPE_IN
            && $call['c_parent_id'] != null
            && $call['c_id'] == $call['first_same_child_c_id']
            && $call['parent_c_call_type_id'] == Call::CALL_TYPE_IN
            && $call['parent_c_parent_id'] == null
        ) {
            $this->firstInAcceptedChildCall($call, $log);
            return;
        }

        if (
            $call['c_call_type_id'] == Call::CALL_TYPE_IN
            && $call['c_parent_id'] != null
            && $call['c_id'] != $call['first_same_child_c_id']
            && $call['parent_c_call_type_id'] == Call::CALL_TYPE_IN
            && $call['parent_c_parent_id'] == null
        ) {
            $this->inTransferInAcceptedChildCall($call, $log);
            return;
        }

        if (
            $call['c_call_type_id'] == Call::CALL_TYPE_IN
            && $call['c_parent_id'] != null
            && $call['c_id'] == $call['first_same_child_c_id']
            && $call['parent_c_call_type_id'] == Call::CALL_TYPE_IN
            && $call['parent_c_parent_id'] != null
        ) {
            $this->outTransferFirstInAcceptedChildCall($call, $log);
            return;
        }

        if (
            $call['c_call_type_id'] == Call::CALL_TYPE_IN
            && $call['c_parent_id'] != null
            && $call['c_id'] != $call['first_same_child_c_id']
            && $call['parent_c_call_type_id'] == Call::CALL_TYPE_IN
            && $call['parent_c_parent_id'] != null
        ) {
            $this->outTransferSecondInAcceptedChildCall($call, $log);
            return;
        }

        if (
            $call['c_call_type_id'] == Call::CALL_TYPE_IN
            && $call['c_parent_id'] != null
            && $call['parent_c_call_type_id'] == Call::CALL_TYPE_OUT
            && $call['c_status_id'] == Call::STATUS_COMPLETED
            && (
                $call['last_child_c_created_user_id'] == null
                || ($call['c_created_user_id'] == $call['last_child_c_created_user_id'] && $call['c_created_user_id'] != null)
            )
        ) {
            // OutTransferInAcceptedParentCall
            return;
        }

        if (
            $call['c_call_type_id'] == Call::CALL_TYPE_IN
            && $call['c_parent_id'] != null
            && $call['parent_c_call_type_id'] == Call::CALL_TYPE_OUT
            && (
                $call['c_status_id'] != Call::STATUS_COMPLETED
                || ($call['last_child_c_created_user_id'] != null && ($call['c_created_user_id'] == null || $call['c_created_user_id'] != $call['last_child_c_created_user_id']))
            )
        ) {
            $this->outTransferInNotAcceptedParentCall($call, $log);
            return;
        }

        if (
            $call['c_call_type_id'] == Call::CALL_TYPE_OUT
            && $call['c_parent_id'] != null
            && $call['first_child_c_id'] == null
            && $call['parent_c_parent_id'] != null
        ) {
            $this->outTransferSecondaryOutChildCall($call, $log);
            return;
        }

        $this->createCallLogs($call, $log, [], []);

    }

    private function outParentCalls($call, array &$log): void
    {
        $callData['cl_group_id'] = $call['c_id'];

        $callRecordData['clr_record_sid'] = $call['first_child_c_recording_sid'];
        $callRecordData['clr_duration'] = $call['first_child_c_recording_duration'];

        $this->createCallLogs(
            $call,
            $log,
            $callData,
            [],
            $callRecordData
        );
    }

    private function outTransferSecondaryOutChildCall($call, array &$log): void
    {
        $call['c_created_user_id'] = null;

        $callData['cl_group_id'] = $call['parent_c_parent_id'];
        $callData['cl_duration'] = $call['c_call_duration'] + (strtotime($call['c_created_dt']) - (strtotime($call['top_parent_c_created_dt']) + $call['top_parent_c_call_duration']));

        $this->createCallLogs(
            $call,
            $log,
            $callData,
            [],
            []
        );
    }

    private function outTransferInNotAcceptedParentCall($call, array &$log): void
    {
        if ($call['c_status_id'] != Call::STATUS_COMPLETED) {
            $call['c_status_id'] = $call['c_status_id'];
        } elseif (
            $call['c_status_id'] == Call::STATUS_COMPLETED
            &&  (int)CallUserAccess::find()
                ->andWhere(['cua_call_id' => $call['c_id']])
                ->andWhere(['>=', 'cua_created_dt', (date('Y-m-d H:i:s', (strtotime($call['last_child_c_created_dt']) + $call['last_child_c_call_duration'])))])
                ->andWhere(['cua_status_id' => CallUserAccess::STATUS_TYPE_ACCEPT])
                ->count() > 0
        ) {
            $call['c_status_id'] = Call::STATUS_FAILED;
        } else {
            $call['c_status_id'] = Call::STATUS_NO_ANSWER;
        }

        $callData['cl_group_id'] = $call['c_parent_id'];

        if ($call['last_child_c_id'] == null) {
            $callData['cl_call_created_dt'] = date('Y-m-d H:i:s', (strtotime($call['parent_c_created_dt']) + $call['parent_c_call_duration']));
        } else {
            $callData['cl_call_created_dt'] = date('Y-m-d H:i:s', (strtotime($call['last_child_c_created_dt']) + $call['last_child_c_call_duration']));
        }
        $callData['cl_call_finished_dt'] = date('Y-m-d H:i:s',(strtotime($call['c_created_dt']) + $call['c_call_duration']));
        $callData['cl_duration'] = strtotime($callData['cl_call_finished_dt']) - strtotime($callData['cl_call_created_dt']);
        $callData['cl_category_id'] = CallLogCategory::GENERAL_LINE;

        $queueData['clq_queue_time'] = $callData['cl_duration'];
        $queueData['clq_access_count'] = CallUserAccess::find()->andWhere(['cua_call_id' => $call['c_id']])->andWhere(['>=', 'cua_created_dt', $callData['cl_call_created_dt']])->count();
        $queueData['clq_is_transfer'] = true;

        $call['c_recording_duration'] = null;
        $call['c_recording_sid'] = null;

        $this->createCallLogs(
            $call,
            $log,
            $callData,
            $queueData
        );
    }

    private function outTransferSecondInAcceptedChildCall($call, array &$log): void
    {
        $current = $call;

        $call['c_parent_id'] = $call['parent_c_parent_id'];

        $callData['cl_duration'] = $call['c_call_duration'] + (strtotime($call['c_created_dt']) - (strtotime($call['prev_child_c_created_dt']) + $call['prev_child_c_call_duration']));

        $callData['cl_category_id'] = CallLogCategory::GENERAL_LINE;
        $callData['cl_is_transfer'] = (
            $call['parent_c_created_user_id'] != $call['c_created_user_id']
            || $call['parent_c_created_user_id'] == null
            || $call['next_child_c_id'] != null
        ) ? true : false;

        $queueData['clq_queue_time'] = (strtotime($call['prev_child_c_created_dt']) + $call['prev_child_c_call_duration']) - strtotime($call['c_created_dt']);
        $queueData['clq_access_count'] = CallUserAccess::find()->andWhere(['cua_call_id' => $current['c_parent_id']])->andWhere(['>', 'cua_created_dt', $call['prev_child_c_created_dt']])->count();
        $queueData['clq_is_transfer'] = true;

        $this->createCallLogs(
            $call,
            $log,
            $callData,
            $queueData
        );
    }

    private function outTransferFirstInAcceptedChildCall($call, array &$log): void
    {
        $current = $call;

        $call['c_parent_id'] = $call['parent_c_parent_id'];

        $callData['cl_duration'] = $call['c_call_duration'] + (strtotime($call['c_created_dt']) - (strtotime($call['top_parent_c_created_dt']) + $call['top_parent_c_call_duration']));

        $callData['cl_category_id'] = CallLogCategory::GENERAL_LINE;
        $callData['cl_is_transfer'] = (
            $call['parent_c_created_user_id'] != $call['c_created_user_id']
            || $call['parent_c_created_user_id'] == null
            || $call['next_child_c_id'] != null
        ) ? true : false;

        $queueData['clq_queue_time'] = strtotime($call['c_created_dt']) - (strtotime($call['top_parent_c_created_dt']) + $call['top_parent_c_call_duration']);
        $queueData['clq_access_count'] = CallUserAccess::find()->andWhere(['cua_call_id' => $current['c_parent_id']])->andWhere(['<=', 'cua_created_dt', $call['c_created_dt']])->count();
        $queueData['clq_is_transfer'] = true;

        $this->createCallLogs(
            $call,
            $log,
            $callData,
            $queueData
        );
    }

    private function inTransferInAcceptedChildCall($call, array &$log): void
    {
        $current = $call;

//        $call['c_parent_id'] = $call['parent_c_status_id'] == Call::STATUS_COMPLETED ? $call['first_same_child_c_id'] : $call['с_parent_id'];
//        $call['c_parent_id'] = $call['first_same_child_c_id'];

//        if (
//            $call['parent_c_created_user_id'] == null
//            || ($call['parent_c_created_user_id'] != $call['last_same_child_c_created_user_id'] && $call['last_same_child_c_created_user_id'] != null)
//            || $call['parent_c_status_id'] != Call::STATUS_COMPLETED
//        ) {
//            $call['c_parent_id'] = $call['c_parent_id'];
//        } else {
//            $call['c_parent_id'] = $call['first_same_child_c_id'];
//        }

        $callData['cl_group_id'] = $call['first_same_child_c_id'];
        $callData['cl_duration'] = $call['c_call_duration'] + (strtotime($call['c_created_dt']) - (strtotime($call['prev_child_c_created_dt']) + $call['prev_child_c_call_duration']));

        $call['c_source_type_id'] = $call['first_same_child_c_call_type_id'];

        $callData['cl_is_transfer'] = (
            $call['parent_c_created_user_id'] != $call['c_created_user_id']
            || $call['parent_c_created_user_id'] == null
            || $call['next_child_c_id'] != null
        ) ? true : false;

        $queueData['clq_queue_time'] = (strtotime($call['prev_child_c_created_dt']) + $call['prev_child_c_call_duration']) - strtotime($call['c_created_dt']);
        $queueData['clq_access_count'] = CallUserAccess::find()->andWhere(['cua_call_id' => $current['c_parent_id']])->andWhere(['>', 'cua_created_dt', $call['prev_child_c_created_dt']])->count();
        $queueData['clq_is_transfer'] = true;

        $this->createCallLogs(
            $call,
            $log,
            $callData,
            $queueData
        );
    }

    private function firstInAcceptedChildCall($call, array &$log): void
    {
        $current = $call;

//        $call['c_parent_id'] = $call['parent_c_status_id'] == Call::STATUS_COMPLETED ? null : $call['c_parent_id'];
//        if (
//            $call['parent_c_created_user_id'] == null
//            || ($call['parent_c_created_user_id'] != $call['last_same_child_c_created_user_id'] && $call['last_same_child_c_created_user_id'] != null)
//            || $call['parent_c_status_id'] != Call::STATUS_COMPLETED
//        ) {
//            $call['c_parent_id'] = $call['c_parent_id'];
//        } else {
//            $call['c_parent_id'] = $call['c_id'];
//        }

        $callData['cl_group_id'] = $call['c_id'];
        $callData['cl_duration'] = $call['c_call_duration'] + (strtotime($call['c_created_dt']) - strtotime($call['parent_c_created_dt']));

        $callData['cl_is_transfer'] = (
            $call['parent_c_created_user_id'] != $call['c_created_user_id']
            || $call['parent_c_created_user_id'] == null
            || $call['next_child_c_id'] != null
        ) ? true : false;

        $queueData['clq_queue_time'] = strtotime($call['parent_c_created_dt']) - strtotime($call['c_created_dt']);
        $queueData['clq_access_count'] = CallUserAccess::find()->andWhere(['cua_call_id' => $current['c_parent_id']])->andWhere(['<=', 'cua_created_dt', $call['c_created_dt']])->count();

        $this->createCallLogs(
            $call,
            $log,
            $callData,
            $queueData
        );
    }

    private function transferInNotAcceptedParentCall($call, array &$log): void
    {
        if ($call['c_status_id'] != Call::STATUS_COMPLETED) {
            $call['c_status_id'] = $call['c_status_id'];
        } elseif (
            $call['c_status_id'] == Call::STATUS_COMPLETED
            &&  (int)CallUserAccess::find()
                ->andWhere(['cua_call_id' => $call['c_id']])
                ->andWhere(['>=', 'cua_created_dt', (date('Y-m-d H:i:s', (strtotime($call['last_child_c_created_dt']) + $call['last_child_c_call_duration'])))])
                ->andWhere(['cua_status_id' => CallUserAccess::STATUS_TYPE_ACCEPT])
                ->count() > 0
        ) {
            $call['c_status_id'] = Call::STATUS_FAILED;
        } else {
            $call['c_status_id'] = Call::STATUS_NO_ANSWER;
        }
        $call['c_source_type_id'] = $call['first_child_c_source_type_id'];

        $callData['cl_group_id'] = $call['first_child_c_id'];
        $callData['cl_call_created_dt'] = date('Y-m-d H:i:s', (strtotime($call['last_child_c_created_dt']) + $call['last_child_c_call_duration']));
        $callData['cl_call_finished_dt'] = date('Y-m-d H:i:s', (strtotime($call['c_created_dt']) + $call['c_call_duration']));
        $callData['cl_duration'] = strtotime($callData['cl_call_finished_dt']) - strtotime($callData['cl_call_created_dt']);

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

    private function directInNotAcceptedParentCall($call, array &$log): void
    {
        $callData['cl_group_id'] = $call['c_id'];

        $queueData['clq_queue_time'] = $call['c_call_duration'];
        $queueData['clq_access_count'] = CallUserAccess::find()->andWhere(['cua_call_id' => $call['c_id']])->count();

        $this->createCallLogs(
            $call,
            $log,
            $callData,
            $queueData
        );
    }

    private function outTransferPrimaryChildCall($call, array &$log): void
    {
        $call['c_parent_id'] = $call['c_id'];

        $callData['cl_is_transfer'] = true;
        $callData['cl_category_id'] = $call['parent_c_source_type_id'];

        $this->createCallLogs(
            $call,
            $log,
            $callData,
            [],
            []
        );
    }

    private function outChildCalls($call, array &$log): void
    {
        $callData['cl_group_id'] = $call['c_id'];

        $this->createCallLogs(
            $call,
            $log,
            $callData,
            [],
            []
        );
    }

    private function outTransferParentCalls($call, array &$log): void
    {
        $callData['cl_group_id'] = $call['c_id'];
        $callData['cl_is_transfer'] = true;

        $callRecordData['clr_record_sid'] = $call['first_child_c_recording_sid'];
        $callRecordData['clr_duration'] = $call['first_child_c_recording_duration'];

        $this->createCallLogs(
            $call,
            $log,
            $callData,
            [],
            $callRecordData
        );
    }

    private function createCallLogs($call, array &$log, array $callData = [], array $queueData = [], array $callRecordData = []): void
    {
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $callLog = new CallLog();
            $callLog->cl_id = $call['c_id'];
            $callLog->cl_call_sid = $callData['cl_call_sid'] ?? $call['c_call_sid'];
            $callLog->cl_type_id = $callData['cl_type_id'] ?? $call['c_call_type_id'];
            $callLog->cl_phone_from = $callData['cl_phone_from'] ?? $call['c_from'];
            $callLog->cl_phone_to = $callData['cl_phone_to'] ?? $call['c_to'];
            $callLog->cl_duration = $callData['cl_duration'] ?? $call['c_call_duration'];
            if ($callLog->cl_duration < 0) {
                $callLog->cl_duration = 0;
            }
            $callLog->cl_user_id = $callData['cl_user_id'] ?? $call['c_created_user_id'];
            $callLog->cl_call_created_dt = $callData['cl_call_created_dt'] ?? $call['c_created_dt'];
            $callLog->cl_call_finished_dt = $callData['cl_call_finished_dt'] ?? date('Y-m-d H:i:s', (strtotime($call['c_created_dt']) + $call['c_call_duration']));
            $callLog->cl_project_id = $callData['cl_project_id'] ?? $call['c_project_id'];
            $callLog->cl_price = $callData['cl_price'] ?? $call['c_price'];
            $callLog->cl_category_id = $callData['cl_category_id'] ?? (in_array($call['c_source_type_id'], [
                Call::SOURCE_GENERAL_LINE, Call::SOURCE_DIRECT_CALL, Call::SOURCE_REDIRECT_CALL, Call::SOURCE_TRANSFER_CALL, Call::SOURCE_CONFERENCE_CALL, Call::SOURCE_REDIAL_CALL
            ], false) ? $call['c_source_type_id'] : null);
            $callLog->cl_is_transfer = $callData['cl_is_transfer'] ?? false;
            $callLog->cl_department_id = $callData['cl_department_id'] ?? $call['c_dep_id'];
            $callLog->cl_client_id = $callData['cl_client_id'] ?? $call['c_client_id'];

            if (!$callLog->cl_client_id) {
                if ($call['c_lead_id']) {
                    if ($lead = Lead::find()->select(['client_id'])->andWhere(['id' => $call['c_lead_id']])->asArray()->one()) {
                        $callLog->cl_client_id = $lead['client_id'];
                    }
                } elseif ($call['c_case_id']) {
                    if ($case = Cases::find()->select(['cs_client_id'])->andWhere(['cs_id' => $call['c_case_id']])->asArray()->one()) {
                        $callLog->cl_client_id = $case['cs_client_id'];
                    }
                }
            }

            if (array_key_exists('cl_group_id', $callData)) {
                $callLog->cl_group_id = $callData['cl_group_id'];
            } else {
                if ($call['c_parent_id']) {
                    $callLog->cl_group_id = $call['c_parent_id'];
                } else {
                    $callLog->cl_group_id = $call['c_id'];
                }
            }

            $callLog->cl_status_id = $callData['cl_status_id'] ?? ($call['c_status_id'] ?: self::convertStatusFromTwStatus($call['c_call_status']));

            if ($call['c_from'] && $call['c_call_type_id'] == Call::CALL_TYPE_OUT) {
                if ($phoneList = PhoneList::find()->select(['pl_id'])->andWhere(['pl_phone_number' => $call['c_from']])->asArray()->one()) {
                    $callLog->cl_phone_list_id = $phoneList['pl_id'];
                }
            } elseif ($call['c_to'] && $call['c_call_type_id'] == Call::CALL_TYPE_IN) {
                if ($phoneList = PhoneList::find()->select(['pl_id'])->andWhere(['pl_phone_number' => $call['c_to']])->asArray()->one()) {
                    $callLog->cl_phone_list_id = $phoneList['pl_id'];
                }
            }

            if ($callLog->cl_call_created_dt) {
                $time = strtotime($callLog->cl_call_created_dt);
                $callLog->cl_year = date('Y', $time);
                $callLog->cl_month = date('m', $time);
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

            $recordSid = $callRecordData['clr_record_sid'] ?? $call['c_recording_sid'];
            if ($recordSid) {
                $callLogRecord = new CallLogRecord([
                    'clr_cl_id' => $callLog->cl_id,
                    'clr_duration' => $callRecordData['clr_duration'] ?? $call['c_recording_duration'],
                    'clr_record_sid' => $recordSid,
                ]);
                if (!$callLogRecord->save()) {
                    throw new \RuntimeException(VarDumper::dumpAsString(['model' => $callLogRecord->toArray(), 'message' => $callLogRecord->getErrors()]));
                }
            }

            if ($queueData) {
                if (isset($queueData['clq_queue_time'])) {
                    $queueData['clq_queue_time'] = abs($queueData['clq_queue_time']);
                }
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
