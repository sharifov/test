<?php

namespace console\controllers;

use common\models\Client;
use common\models\DepartmentEmailProject;
use common\models\DepartmentPhoneProject;
use common\models\UserProjectParams;
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
