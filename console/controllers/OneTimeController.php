<?php

namespace console\controllers;

use common\components\jobs\CreateSaleFromBOJob;
use common\models\Client;
use common\models\DepartmentEmailProject;
use common\models\DepartmentPhoneProject;
use common\models\UserProjectParams;
use sales\helpers\app\AppHelper;
use sales\model\emailList\entity\EmailList;
use sales\model\phoneList\entity\PhoneList;
use thamtech\uuid\helpers\UuidHelper;
use Yii;
use yii\console\Controller;
use yii\db\Query;
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

    /**
     * @param string $fromDate
     * @param string $toDate
     * @param int $status
     */
    public function actionSaleToCase(string $fromDate, string $toDate, int $status): void
    {
        echo Console::renderColoredString('%g --- Start %W[' . date('Y-m-d H:i:s') . '] %g'. self::class . ':' . $this->action->id .' %n'), PHP_EOL;
        $time_start = microtime(true);

        $fromDate = date('Y-m-d', strtotime($fromDate));
        $toDate = date('Y-m-d', strtotime($toDate));
        $processed = 0;

        try {
            $cases = $this->getCaseNotSale($fromDate, $toDate, $status);
            Console::startProgress(0, count($cases));

            foreach ($cases as $key => $value) {
                $job = new CreateSaleFromBOJob();
                $job->case_id = $value['cs_id'];
                $job->phone = $this->getPhoneByClient($value['cs_client_id'])['phone'];
                Yii::$app->queue_job->priority(100)->push($job);
                $processed ++;
                Console::updateProgress($processed, count($cases));
            }
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableFormatter($throwable),
                'OneTimeController:actionSaleToCase:Throwable' );
            echo Console::renderColoredString('%r --- Error : '. $throwable->getMessage() .' %n'), PHP_EOL;
        }

        Console::endProgress(false);
        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);
        echo Console::renderColoredString('%g --- Execute Time: %W['. $time .' s] %gProcessed: %W[' . $processed . '] %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- End : %W[' . date('Y-m-d H:i:s') . '] %g'. self::class . ':' . $this->action->id .' %n'), PHP_EOL;
    }


    /**
     * @param string $fromDate
     * @param string $toDate
     * @param int $status
     * @param array|int[] $notType
     * @return array|\yii\db\DataReader
     * @throws \yii\db\Exception
     */
    protected function getCaseNotSale(string $fromDate, string $toDate, int $status, string $notType = '9')
    {
        return Yii::$app->db->createCommand(
            'SELECT 
                    cases.cs_id,
                    cases.cs_client_id
                FROM
                    cases
                LEFT JOIN
                    case_sale ON case_sale.css_cs_id = cases.cs_id
                INNER JOIN
                    client_phone 
                    ON 
                    client_phone.client_id = cases.cs_client_id
                    AND 
                    client_phone.type NOT IN (:not_type)
                WHERE
                    case_sale.css_cs_id IS NULL
                    AND DATE(cases.cs_created_dt) BETWEEN :from_date AND :to_date
                    AND cases.cs_status = :status
                GROUP BY 
                    cases.cs_id,
                    cases.cs_client_id',
            [
                ':from_date' => $fromDate,
                ':to_date' => $toDate,
                ':status' => $status,
                ':not_type' => $notType,
            ]
        )->queryAll();
    }

    /**
     * @param int $clientId
     * @param string $notType
     * @return array|false|\yii\db\DataReader
     * @throws \yii\db\Exception
     */
    protected function getPhoneByClient(int $clientId, string $notType = '9')
    {
        return Yii::$app->db->createCommand(
            'SELECT 
                    client_phone.phone
                FROM
                    client_phone
                WHERE 
                    client_id = :client_id
                    AND
                    client_phone.type NOT IN (:not_type)	
                ORDER BY 
                    created DESC
                LIMIT 1',
            [
                ':client_id' => $clientId,
                ':not_type' => $notType,
            ]
        )->queryOne();
    }
}
