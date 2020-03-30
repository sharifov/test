<?php

namespace console\controllers;

use common\models\Call;
use common\models\Client;
use common\models\DepartmentEmailProject;
use common\models\DepartmentPhoneProject;
use common\models\UserProjectParams;
use sales\model\callLog\entity\callLog\CallLog;
use sales\model\callLog\entity\callLogCase\CallLogCase;
use sales\model\callLog\entity\callLogLead\CallLogLead;
use sales\model\callLog\entity\callLogRecord\CallLogRecord;
use sales\model\emailList\entity\EmailList;
use sales\model\phoneList\entity\PhoneList;
use thamtech\uuid\helpers\UuidHelper;
use yii\console\Controller;
use yii\helpers\BaseConsole;
use yii\helpers\Console;
use yii\helpers\VarDumper;

class OneTimeController extends Controller
{
    public $limit;

    public function options($actionID)
    {
        if ($actionID === 'generate-client-uuid') {
            return array_merge(parent::options($actionID), [
                'limit'
            ]);
        }
        return parent::options($actionID);
    }

    public function actionTruncateCallLogs(): void
    {
        $db  = \Yii::$app->db;
        $db->createCommand('SET FOREIGN_KEY_CHECKS=0;')->execute();
        $db->createCommand()->truncateTable('{{%call_log}}')->execute();
        $db->createCommand()->truncateTable('{{%call_log_lead}}')->execute();
        $db->createCommand()->truncateTable('{{%call_log_case}}')->execute();
        $db->createCommand()->truncateTable('{{%call_log_queue}}')->execute();
        $db->createCommand()->truncateTable('{{%call_log_record}}')->execute();
        $db->createCommand('SET FOREIGN_KEY_CHECKS=1;')->execute();
    }

    public function actionMigrateCallsToCallLog()
    {
         $logs = [];
         $n = 0;
         Console::startProgress(0, 10, 'Counting objects: ', false);
         foreach (Call::find()->orderBy(['c_id' => SORT_ASC])->limit(10)->all() as $call) {
             $this->createCallLogs($call, $logs);
             $n++;
             Console::updateProgress($n, 10);
         }
         Console::endProgress("done." . PHP_EOL);
         if ($logs) {
             foreach ($logs as $log) {
                 print_r($log);
             }
         }
    }

    private function createCallLogs(Call $call, array &$log): void
    {
        if (CallLog::find()->andWhere(['cl_id' => $call->c_id])->exists()) {
            $log[] = [
                'Call Id' =>  $call->c_id,
                'Message' => ' is already exist',
            ];
            return;
        }

        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $callLog = new CallLog();
            $callLog->cl_id = $call->c_id;
            $callLog->cl_call_sid = $call->c_call_sid;
            $callLog->cl_type_id = $call->c_call_type_id;
            $callLog->cl_phone_from = $call->c_from;
            $callLog->cl_phone_to = $call->c_to;
            $callLog->cl_duration = $call->c_call_duration;
            $callLog->cl_user_id = $call->c_created_user_id;
            $callLog->cl_call_created_dt = $call->c_created_dt;
//        $callLog->cl_call_finished_dt = $call->c_updated_dt;
            $callLog->cl_project_id = $call->c_project_id;
            $callLog->cl_price = $call->c_price;
            $callLog->cl_category_id = in_array($call->c_source_type_id,[
                Call::SOURCE_GENERAL_LINE, Call::SOURCE_DIRECT_CALL, Call::SOURCE_REDIRECT_CALL, Call::SOURCE_CONFERENCE_CALL, Call::SOURCE_REDIAL_CALL
            ], false) ? $call->c_source_type_id : null;
            $callLog->cl_is_transfer = $call->isTransfer();
            $callLog->cl_department_id = $call->c_dep_id;
            $callLog->cl_client_id = $call->c_client_id;
            $callLog->cl_parent_id = $call->c_parent_id;
            $callLog->cl_status_id = in_array($call->c_status_id,[
                Call::STATUS_COMPLETED, Call::STATUS_BUSY, Call::STATUS_NO_ANSWER, Call::STATUS_FAILED, Call::STATUS_CANCELED
            ], false) ? $call->c_status_id : Call::STATUS_CANCELED;
            if ($call->isOut() && $call->c_from) {
                if ($phoneList = PhoneList::find()->select(['pl_id'])->andWhere(['pl_phone_number' => $call->c_from])->asArray()->one()) {
                    $callLog->cl_phone_list_id = $phoneList['pl_id'];
                }
            } elseif ($call->isIn() && $call->c_to) {
                if ($phoneList = PhoneList::find()->select(['pl_id'])->andWhere(['pl_phone_number' => $call->c_to])->asArray()->one()) {
                    $callLog->cl_phone_list_id = $phoneList['pl_id'];
                }
            }

            if (!$callLog->save()) {
                throw new \RuntimeException(VarDumper::dumpAsString([$callLog->getErrors()]));
            }

            if ($call->c_lead_id) {
                $callLogLead = new CallLogLead([
                    'cll_cl_id' => $callLog->cl_id,
                    'cll_lead_id' => $call->c_lead_id
                ]);
                if (!$callLogLead->save()) {
                    throw new \RuntimeException(VarDumper::dumpAsString([$callLogLead->getErrors()]));
                }
            }

            if ($call->c_case_id) {
                $callLogCase = new CallLogCase([
                    'clc_cl_id' => $callLog->cl_id,
                    'clc_case_id' => $call->c_case_id
                ]);
                if (!$callLogCase->save()) {
                    throw new \RuntimeException(VarDumper::dumpAsString([$callLogCase->getErrors()]));
                }
            }

            if ($call->c_recording_duration || $call->c_recording_sid) {
                $callLogRecord = new CallLogRecord([
                    'clr_cl_id' => 'asdasd.as.da.sd',
                    'clr_duration' => $call->c_recording_duration,
                    'clr_record_sid' => $call->c_recording_sid,
                ]);
                if (!$callLogRecord->save()) {
                    throw new \RuntimeException(VarDumper::dumpAsString([$callLogRecord->getErrors()]));
                }
            }

            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $log[] = [
                'Call Id' =>  $call->c_id,
                'Error' => $e->getMessage(),
            ];
        }
    }

    public function actionPhoneEmailListUpdate(): void
    {
        $report = [];

        printf(PHP_EOL . '--- Start [' . date('Y-m-d H:i:s') . '] %s ---' . PHP_EOL, $this->ansiFormat(self::class . '\\' . $this->action->id, Console::FG_YELLOW));
        printf(PHP_EOL);
        $time_start = microtime(true);

        foreach (UserProjectParams::find()->with('uppProject', 'uppDep', 'uppUser')->all() as $upp) {
            $title = [];
            if ($upp->uppProject) {
                $title[] = $upp->uppProject->name;
            }
            if ($upp->uppDep) {
                $title[] = $upp->uppDep->dep_name;
            }
            if ($upp->uppUser) {
                $title[] = $upp->uppUser->username;
            }
            if ($upp->upp_tw_phone_number) {
                if (!$phoneList = PhoneList::find()->andWhere(['pl_phone_number' => $upp->upp_tw_phone_number])->one()) {
                    $phoneList = new Phonelist([
                        'pl_phone_number' => $upp->upp_tw_phone_number,
                        'pl_enabled' => true,
                        'pl_title' => implode('-', $title),
                    ]);
                    if ($phoneList->save()) {
                        $upp->upp_phone_list_id = $phoneList->pl_id;
                        if (!$upp->save()) {
                            $report[] = VarDumper::dumpAsString([
                                'section' => 'UserProjectParams:1',
                                'error' => $upp->getErrors(),
                                'model' => $upp->toArray()
                            ]);
                        }
                    } else {
                        $report[] = VarDumper::dumpAsString([
                            'section' => 'UserProjectParams:2',
                            'error' => $phoneList->getErrors(),
                            'model' => $phoneList->toArray()
                        ]);
                    }
                } else {
                    $upp->upp_phone_list_id = $phoneList->pl_id;
                    if (!$upp->save()) {
                        $report[] = VarDumper::dumpAsString([
                            'section' => 'UserProjectParams:3',
                            'error' => $upp->getErrors(),
                            'model' => $upp->toArray()
                        ]);
                    } else {
                        $phoneList->pl_title = implode('-', $title);
                        if (!$phoneList->save()) {
                            $report[] = VarDumper::dumpAsString([
                                'section' => 'UserProjectParams:4',
                                'error' => $phoneList->getErrors(),
                                'model' => $phoneList->toArray()
                            ]);
                        }
                    }
                }
            }
            if ($upp->upp_email) {
                if (!$emailList = EmailList::find()->andWhere(['el_email' => $upp->upp_email])->one()) {
                    $emailList = new EmailList([
                        'el_email' => $upp->upp_email,
                        'el_enabled' => true,
                        'el_title' => implode('-', $title),
                    ]);
                    if ($emailList->save()) {
                        $upp->upp_email_list_id = $emailList->el_id;
                        if (!$upp->save()) {
                            $report[] = VarDumper::dumpAsString([
                                'section' => 'UserProjectParams:5',
                                'error' => $upp->getErrors(),
                                'model' => $upp->toArray()
                            ]);
                        }
                    } else {
                        $report[] = VarDumper::dumpAsString([
                            'section' => 'UserProjectParams:6',
                            'error' => $emailList->getErrors(),
                            'model' => $emailList->toArray()
                        ]);
                    }
                } else {
                    $upp->upp_email_list_id = $emailList->el_id;
                    if (!$upp->save()) {
                        $report[] = VarDumper::dumpAsString([
                            'section' => 'UserProjectParams:7',
                            'error' => $upp->getErrors(),
                            'model' => $upp->toArray()
                        ]);
                    } else {
                        $emailList->el_title = implode('-', $title);
                        if (!$emailList->save()) {
                            $report[] = VarDumper::dumpAsString([
                                'section' => 'UserProjectParams:8',
                                'error' => $emailList->getErrors(),
                                'model' => $emailList->toArray()
                            ]);
                        }
                    }
                }
            }
        }
        foreach (DepartmentPhoneProject::find()->with(['dppProject', 'dppDep'])->all() as $dpp) {
            if ($dpp->dpp_phone_number) {
                $title = [];
                if ($dpp->dppProject) {
                    $title[] = $dpp->dppProject->name;
                }
                if ($dpp->dppDep) {
                    $title[] = $dpp->dppDep->dep_name;
                }
                if (!$phoneList = PhoneList::find()->andWhere(['pl_phone_number' => $dpp->dpp_phone_number])->one()) {
                    $phoneList = new Phonelist([
                        'pl_phone_number' => $dpp->dpp_phone_number,
                        'pl_enabled' => true,
                        'pl_title' => implode('-', $title),
                    ]);
                    if ($phoneList->save()) {
                        $dpp->dpp_phone_list_id = $phoneList->pl_id;
                        if (!$dpp->save()) {
                            $report[] = VarDumper::dumpAsString([
                                'section' => 'DepartmentPhoneProject:1',
                                'error' => $dpp->getErrors(),
                                'model' => $dpp->toArray()
                            ]);
                        }
                    } else {
                        $report[] = VarDumper::dumpAsString([
                            'section' => 'DepartmentPhoneProject:2',
                            'error' => $phoneList->getErrors(),
                            'model' => $phoneList->toArray()
                        ]);
                    }
                } else {
                    $dpp->dpp_phone_list_id = $phoneList->pl_id;
                    if (!$dpp->save()) {
                        $report[] = VarDumper::dumpAsString([
                            'section' => 'DepartmentPhoneProject:3',
                            'error' => $dpp->getErrors(),
                            'model' => $dpp->toArray()
                        ]);
                    } else {
                        $phoneList->pl_title = implode('-', $title);
                        if (!$phoneList->save()) {
                            $report[] = VarDumper::dumpAsString([
                                'section' => 'DepartmentPhoneProject:4',
                                'error' => $phoneList->getErrors(),
                                'model' => $phoneList->toArray()
                            ]);
                        }
                    }
                }
            }
        }
        foreach (DepartmentEmailProject::find()->all() as $dep) {
            if ($dep->dep_email) {
                $title = [];
                if ($dep->depProject) {
                    $title[] = $dep->depProject->name;
                }
                if ($dep->depDep) {
                    $title[] = $dep->depDep->dep_name;
                }
                if (!$emailList = EmailList::find()->andWhere(['el_email' => $dep->dep_email])->one()) {
                    $emailList = new EmailList([
                        'el_email' => $dep->dep_email,
                        'el_enabled' => true,
                        'el_title' => implode('-', $title),
                    ]);
                    if ($emailList->save()) {
                        $dep->dep_email_list_id = $emailList->el_id;
                        if (!$dep->save()) {
                            $report[] = VarDumper::dumpAsString([
                                'section' => 'DepartmentEmailProject:1',
                                'error' => $dep->getErrors(),
                                'model' => $dep->toArray()
                            ]);
                        }
                    } else {
                        $report[] = VarDumper::dumpAsString([
                            'section' => 'DepartmentEmailProject:2',
                            'error' => $emailList->getErrors(),
                            'model' => $emailList->toArray()
                        ]);
                    }
                } else {
                    $dep->dep_email_list_id = $emailList->el_id;
                    if (!$dep->save()) {
                        $report[] = VarDumper::dumpAsString([
                            'section' => 'DepartmentEmailProject:3',
                            'error' => $dep->getErrors(),
                            'model' => $dep->toArray()
                        ]);
                    } else {
                        $emailList->el_title = implode('-', $title);
                        if (!$emailList->save()) {
                            $report[] = VarDumper::dumpAsString([
                                'section' => 'DepartmentEmailProject:4',
                                'error' => $emailList->getErrors(),
                                'model' => $emailList->toArray()
                            ]);
                        }
                    }
                }
            }
        }

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);
        if ($report) {
            print_r($report);
        }
        printf(PHP_EOL . 'Execute Time: %s' . PHP_EOL, $this->ansiFormat($time . ' s', Console::FG_RED));
        printf(PHP_EOL . ' --- End [' . date('Y-m-d H:i:s') . '] %s ---' . PHP_EOL . PHP_EOL, $this->ansiFormat(self::class . '\\' . $this->action->id, Console::FG_YELLOW));
    }

    public function actionGenerateClientUuid($limit): void
    {
        printf(PHP_EOL . '--- Start [' . date('Y-m-d H:i:s') . '] %s ---' . PHP_EOL, $this->ansiFormat(self::class . '\\' . $this->action->id, Console::FG_YELLOW));

        printf(PHP_EOL);

//        $limit = BaseConsole::input('Enter Limit records: ');

        $limit = (int)$limit;

        $time_start = microtime(true);

        $logs = [];
        foreach (Client::find()->andWhere(['IS', 'uuid', null])->limit($limit)->orderBy(['id' => SORT_ASC])->all() as $client) {
            $client->uuid = UuidHelper::uuid();
            if (!$client->save()) {
                $logs[$client->id] = VarDumper::dumpAsString($client->getErrors());
            }
        }

        if ($logs) {
            echo 'Errors: ' . PHP_EOL;
            foreach ($logs as $key => $log) {
                echo  'Client Id: ' . $key . ' error: ' . $log  . PHP_EOL;
            }
            echo PHP_EOL;
        }

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);

        printf(PHP_EOL . 'Execute Time: %s' . PHP_EOL, $this->ansiFormat($time . ' s', Console::FG_RED));
        printf(PHP_EOL . ' --- End [' . date('Y-m-d H:i:s') . '] %s ---' . PHP_EOL . PHP_EOL, $this->ansiFormat(self::class . '\\' . $this->action->id, Console::FG_YELLOW));
    }

    public function actionDropLeadLogsTable(): void
    {
        printf(PHP_EOL . '--- Start [' . date('Y-m-d H:i:s') . '] %s ---' . PHP_EOL, $this->ansiFormat(self::class . '\\' . $this->action->id, Console::FG_YELLOW));
        $time_start = microtime(true);

        \Yii::$app->db->createCommand()->dropTable('{{%lead_logs}}')->execute();

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);

        printf(PHP_EOL . 'Execute Time: %s' . PHP_EOL, $this->ansiFormat($time . ' s', Console::FG_RED));
        printf(PHP_EOL . ' --- End [' . date('Y-m-d H:i:s') . '] %s ---' . PHP_EOL . PHP_EOL, $this->ansiFormat(self::class . '\\' . $this->action->id, Console::FG_YELLOW));
    }
}
