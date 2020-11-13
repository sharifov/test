<?php

namespace console\controllers;

use common\components\jobs\CreateSaleFromBOJob;
use common\models\Call;
use common\models\CaseSale;
use common\components\jobs\UpdateSaleFromBOJob;
use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Conference;
use common\models\DepartmentEmailProject;
use common\models\DepartmentPhoneProject;
use common\models\Email;
use common\models\Lead;
use common\models\Sms;
use common\models\UserProjectParams;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesStatus;
use sales\helpers\app\AppHelper;
use sales\model\clientChat\entity\ClientChat;
use sales\model\emailList\entity\EmailList;
use sales\model\phoneList\entity\PhoneList;
use thamtech\uuid\helpers\UuidHelper;
use Yii;
use yii\console\Controller;
use yii\db\Expression;
use yii\db\Query;
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

    /**
     * @param string $fromDate
     * @param string $toDate
     * @param int $status
     */
    public function actionSaleToCase(string $fromDate, string $toDate, int $status): void
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ .' %n'), PHP_EOL;
        $time_start = microtime(true);

        $fromDate = date('Y-m-d', strtotime($fromDate));
        $toDate = date('Y-m-d', strtotime($toDate));
        $processed = $countCases = 0;

        try {
            $cases = $this->getCaseNotSale($fromDate, $toDate, $status);
            $countCases = count($cases);
            Console::startProgress(0, $countCases);

            foreach ($cases as $key => $value) {
                $job = new CreateSaleFromBOJob();
                $job->case_id = $value['cs_id'];
                $job->phone = $this->getPhoneByClient($value['cs_client_id'])['phone'];
                Yii::$app->queue_job->priority(100)->push($job);
                $processed ++;
                Console::updateProgress($processed, $countCases);
            }
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableFormatter($throwable),
                'OneTimeController:actionSaleToCase:Throwable' );
            echo Console::renderColoredString('%r --- Error : '. $throwable->getMessage() .' %n'), PHP_EOL;
        }

        Console::endProgress(false);
        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);
        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time .
            ' s] %gFind cases: %w[' . $countCases . '] %g Added to queue: %w[' . $processed . '] %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . '] %g'. self::class . ':' . __FUNCTION__ .' %n'), PHP_EOL;
    }

    /**
     * @param string $fromDate
     * @param string $toDate
     * @throws \yii\db\Exception
     */
    public function actionSaleRefundRulesToCase(string $fromDate = '2010-01-01', string $toDate = '2021-01-01'): void
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;
        $time_start = microtime(true);

        $fromDate = date('Y-m-d', strtotime($fromDate));
        $toDate = date('Y-m-d', strtotime($toDate));
        $processed = $countItems = 0;

        $cases = Yii::$app->db->createCommand(
        '
            SELECT 
                cases.cs_id,
                cases.cs_client_id,
                case_sale.css_sale_id
            FROM
                cases
            INNER JOIN
                client_phone 
                ON 
                client_phone.client_id = cases.cs_client_id
                AND 
                (client_phone.type NOT IN (:not_type) OR client_phone.type IS NULL)
            LEFT JOIN 
                case_sale
                ON
                cases.cs_id = case_sale.css_cs_id
            WHERE
                DATE(cases.cs_created_dt) BETWEEN :from_date AND :to_date
                AND 
                (
                    cases.cs_status = :statuses_pending
                    OR
                    cases.cs_status = :statuses_processing
                    OR 
                    cases.cs_status = :statuses_follow_up
                )
            GROUP BY 
                cases.cs_id,
                cases.cs_client_id,
                case_sale.css_sale_id
            ',
            [
                ':from_date' => $fromDate,
                ':to_date' => $toDate,
                ':statuses_pending' => CasesStatus::STATUS_PENDING,
                ':statuses_processing' => CasesStatus::STATUS_PROCESSING,
                ':statuses_follow_up' => CasesStatus::STATUS_FOLLOW_UP,
                ':not_type' => 9,
            ]
        )->queryAll();

        try {
            $countItems = count($cases);
            Console::startProgress(0, $countItems);

            foreach ($cases as $key => $value) {
                if (empty($value['css_sale_id'])) {
                    $job = new CreateSaleFromBOJob();
                    $job->case_id = $value['cs_id'];
                    $job->phone = $this->getPhoneByClient($value['cs_client_id'])['phone'];
                    Yii::$app->queue_job->priority(10)->push($job);
                } else {
                    $job = new UpdateSaleFromBOJob();
                    $job->caseId = $value['cs_id'];
                    $job->saleId = $value['css_sale_id'];
                    Yii::$app->queue_job->priority(10)->push($job);
                }

                $processed ++;
                Console::updateProgress($processed, $countItems);
            }
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableFormatter($throwable),
                'OneTimeController:actionSaleToCase:Throwable' );
            echo Console::renderColoredString('%r --- Error : '. $throwable->getMessage() .' %n'), PHP_EOL;
        }
        Console::endProgress(false);

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);

        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time .
            ' s] %gFind cases: %w[' . $countItems . '] %g Added to queue: %w[' . $processed . '] %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ .' %n'), PHP_EOL;
    }

    public function actionSaleDataToJson(?string $fromDate = null, ?string $toDate = null): void
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ .' %n'), PHP_EOL;

        $processed = 0;
        $time_start = microtime(true);

        $fromDate = $fromDate ?? CaseSale::find()->min('css_created_dt');
        $toDate = $toDate ?? CaseSale::find()->max('css_created_dt');
        $fromDate = date('Y-m-d', strtotime($fromDate));
        $toDate = date('Y-m-d', strtotime($toDate));

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $processed = Yii::$app->db->createCommand(
                'UPDATE
                        case_sale
                    SET 
                        css_sale_data = JSON_UNQUOTE(css_sale_data),
                        css_sale_data_updated = JSON_UNQUOTE(css_sale_data_updated)
                    WHERE                    
                        DATE(css_created_dt) BETWEEN :from_date AND :to_date
                        AND
                        JSON_TYPE(css_sale_data) = :json_type
                ',
                [
                    ':from_date' => $fromDate,
                    ':to_date' => $toDate,
                    ':json_type' => 'STRING',
                ]
            )->execute();

            $transaction->commit();
        } catch (\Throwable $throwable) {
            $transaction->rollBack();
            Yii::error(AppHelper::throwableFormatter($throwable),
                'OneTimeController:actionSaleDataToJson:Throwable' );
            echo Console::renderColoredString('%r --- Error : ' . $throwable->getMessage() . ' %n'), PHP_EOL;
        }

        $left = CaseSale::find()
            ->andWhere(['=', new Expression('JSON_TYPE(css_sale_data)'), 'STRING'])
            ->count();

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);
        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time .
            ' s] %g Processed: %w[' . $processed . '] %g How many records left %w[' . $left . '] %g %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;
    }

    public function actionCalculateConferenceDuration()
    {
        $report = [];

        printf(PHP_EOL . '--- Start [' . date('Y-m-d H:i:s') . '] %s ---' . PHP_EOL, $this->ansiFormat(self::class . '\\' . $this->action->id, Console::FG_YELLOW));
        printf(PHP_EOL);
        $time_start = microtime(true);

        $query = Conference::find()
            ->andWhere(['IS', 'cf_duration', null])
            ->orderBy(['cf_id' => SORT_ASC]);
        $count = (clone $query)->count();

        Console::startProgress(0, $count);
        $processed = 0;

        foreach ($query->batch() as $conferences) {
            /** @var Conference $conference */
            foreach ($conferences as $conference) {
                $conference->calculateDuration();
                if (!$conference->save()) {
                    $report[] = VarDumper::dumpAsString([
                        'id' => $conference->cf_id,
                        'errors' => $conference->getErrors(),
                        'model' => $conference->getAttributes(),
                    ]);
                }
                $processed ++;
                Console::updateProgress($processed, $count);
            }
        }
        Console::endProgress(false);

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);
        if ($report) {
            print_r($report);
        }
        printf(PHP_EOL . 'Execute Time: %s' . PHP_EOL, $this->ansiFormat($time . ' s', Console::FG_RED));
        printf(PHP_EOL . ' --- End [' . date('Y-m-d H:i:s') . '] %s ---' . PHP_EOL . PHP_EOL, $this->ansiFormat(self::class . '\\' . $this->action->id, Console::FG_YELLOW));
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
                    (client_phone.type NOT IN (:not_type) OR client_phone.type IS NULL)
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
                    (client_phone.type NOT IN (:not_type) OR client_phone.type IS NULL)	
                ORDER BY 
                    created DESC
                LIMIT 1',
            [
                ':client_id' => $clientId,
                ':not_type' => $notType,
            ]
        )->queryOne();
    }

    public function actionUpdateClientProject(): void
    {
        $offset = BaseConsole::input('Start from client number: ');
        $limit = BaseConsole::input('Limit: ');

        $offset = (int)$offset;
        $limit = (int)$limit;

        if ($offset < 1) {
            echo 'Start must be > 0' . PHP_EOL;
            return;
        }

        if ($limit < 1) {
            echo 'Limit must be > 0' . PHP_EOL;
            return;
        }

        $offset--;

        printf(PHP_EOL . '--- Start [' . date('Y-m-d H:i:s') . '] %s ---' . PHP_EOL, $this->ansiFormat(self::class . '\\' . $this->action->id, Console::FG_YELLOW));
        printf(PHP_EOL);
        $time_start = microtime(true);

        $clients = Client::find()
            ->offset($offset)
            ->limit($limit)
            ->asArray()
            ->orderBy(['id' => SORT_ASC])
            ->all();

        $count = count($clients);

        Console::startProgress(0, $count);
        $processed = 0;

        foreach ($clients as $client) {

            $processed ++;
            Console::updateProgress($processed, $count);

            if ($client['cl_project_id']) {
                continue;
            }

            $projects = [];

            $models = Lead::find()->select(['project_id', 'created'])->andWhere(['client_id' => $client['id']])->andWhere(['IS NOT', 'project_id', null])->orderBy(['id' => SORT_ASC])->asArray()->all();
            foreach ($models as $model) {
                if (empty($projects[$model['project_id']])) {
                    $projects[$model['project_id']] = [
                        'date' => $model['created'],
                        'type' => Client::TYPE_CREATE_LEAD,
                    ];
                } else {
                    $oldDate = strtotime($projects[$model['project_id']]['date']);
                    $newDate = strtotime($model['created']);
                    if ($newDate < $oldDate) {
                        $projects[$model['project_id']] = [
                            'date' => $model['created'],
                            'type' => Client::TYPE_CREATE_LEAD,
                        ];
                    }
                }
            }

            $models = Cases::find()->select(['cs_project_id', 'cs_created_dt'])->andWhere(['cs_client_id' => $client['id']])->andWhere(['IS NOT', 'cs_project_id', null])->orderBy(['cs_id' => SORT_ASC])->asArray()->all();
            foreach ($models as $model) {
                if (empty($projects[$model['cs_project_id']])) {
                    $projects[$model['cs_project_id']] = [
                        'date' => $model['cs_created_dt'],
                        'type' => Client::TYPE_CREATE_CASE,
                    ];
                } else {
                    $oldDate = strtotime($projects[$model['cs_project_id']]['date']);
                    $newDate = strtotime($model['cs_created_dt']);
                    if ($newDate < $oldDate) {
                        $projects[$model['cs_project_id']] = [
                            'date' => $model['cs_created_dt'],
                            'type' => Client::TYPE_CREATE_CASE,
                        ];
                    }
                }
            }

            $models = Call::find()->select(['c_project_id', 'c_created_dt'])->andWhere(['c_client_id' => $client['id']])->andWhere(['IS NOT', 'c_project_id', null])->orderBy(['c_id' => SORT_ASC])->asArray()->all();
            foreach ($models as $model) {
                if (empty($projects[$model['c_project_id']])) {
                    $projects[$model['c_project_id']] = [
                        'date' => $model['c_created_dt'],
                        'type' => Client::TYPE_CREATE_CALL,
                    ];
                } else {
                    $oldDate = strtotime($projects[$model['c_project_id']]['date']);
                    $newDate = strtotime($model['c_created_dt']);
                    if ($newDate < $oldDate) {
                        $projects[$model['c_project_id']] = [
                            'date' => $model['c_created_dt'],
                            'type' => Client::TYPE_CREATE_CALL,
                        ];
                    }
                }
            }

            $models = Sms::find()->select(['s_project_id', 's_created_dt'])->andWhere(['s_client_id' => $client['id']])->andWhere(['IS NOT', 's_project_id', null])->orderBy(['s_id' => SORT_ASC])->asArray()->all();
            foreach ($models as $model) {
                if (empty($projects[$model['s_project_id']])) {
                    $projects[$model['s_project_id']] = [
                        'date' => $model['s_created_dt'],
                        'type' => Client::TYPE_CREATE_SMS,
                    ];
                } else {
                    $oldDate = strtotime($projects[$model['s_project_id']]['date']);
                    $newDate = strtotime($model['s_created_dt']);
                    if ($newDate < $oldDate) {
                        $projects[$model['s_project_id']] = [
                            'date' => $model['s_created_dt'],
                            'type' => Client::TYPE_CREATE_SMS,
                        ];
                    }
                }
            }

            $models = Email::find()->select(['e_project_id', 'e_created_dt'])->andWhere(['e_client_id' => $client['id']])->andWhere(['IS NOT', 'e_project_id', null])->orderBy(['e_id' => SORT_ASC])->asArray()->all();
            foreach ($models as $model) {
                if (empty($projects[$model['e_project_id']])) {
                    $projects[$model['e_project_id']] = [
                        'date' => $model['e_created_dt'],
                        'type' => Client::TYPE_CREATE_EMAIL,
                    ];
                } else {
                    $oldDate = strtotime($projects[$model['e_project_id']]['date']);
                    $newDate = strtotime($model['e_created_dt']);
                    if ($newDate < $oldDate) {
                        $projects[$model['e_project_id']] = [
                            'date' => $model['e_created_dt'],
                            'type' => Client::TYPE_CREATE_EMAIL,
                        ];
                    }
                }
            }

            $models = ClientChat::find()->select(['cch_project_id', 'cch_created_dt'])->andWhere(['cch_client_id' => $client['id']])->andWhere(['IS NOT', 'cch_project_id', null])->orderBy(['cch_id' => SORT_ASC])->asArray()->all();
            foreach ($models as $model) {
                if (empty($projects[$model['cch_project_id']])) {
                    $projects[$model['cch_project_id']] = [
                        'date' => $model['cch_created_dt'],
                        'type' => Client::TYPE_CREATE_CLIENT_CHAT,
                    ];
                } else {
                    $oldDate = strtotime($projects[$model['cch_project_id']]['date']);
                    $newDate = strtotime($model['cch_created_dt']);
                    if ($newDate < $oldDate) {
                        $projects[$model['cch_project_id']] = [
                            'date' => $model['cch_created_dt'],
                            'type' => Client::TYPE_CREATE_CLIENT_CHAT,
                        ];
                    }
                }
            }

            $firstData = [];
            foreach ($projects as $key => $project) {
                if (empty($firstData)) {
                    $firstData = [
                        'projectId' => $key,
                        'date' => $project['date'],
                        'type' => $project['type']
                    ];
                } else {
                    $oldDate = strtotime($firstData['date']);
                    $newDate = strtotime($project['date']);
                    if ($newDate < $oldDate) {
                        $firstData = [
                            'projectId' => $key,
                            'date' => $project['date'],
                            'type' => $project['type']
                        ];
                    }
                }
            }

//            echo 'ClientId: ' . $client['id'];
            if ($firstData) {
//                echo ' First ProjectId: ' . $firstData['projectId'] . ' Type: ' . $firstData['type'] . ' date: ' . $firstData['date'];
                Client::updateAll(['cl_project_id' => $firstData['projectId'], 'cl_type_create' => $firstData['type']], 'id = ' . $client['id']);
                unset($projects[$firstData['projectId']]);
            }

            foreach ($projects as $key => $project) {
                $exist = Client::find()->andWhere(['cl_project_id' => $key, 'parent_id' => $client['id']])->exists();
                if ($exist) {
                    continue;
                }
                $this->cloneClientWithProject($client, $key, $project['type'], $project['date']);
            }

//            echo PHP_EOL;
//            VarDumper::dump($projects);
//            echo PHP_EOL;

        }

        Console::endProgress(false);

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);

        printf(PHP_EOL . 'Execute Time: %s' . PHP_EOL, $this->ansiFormat($time . ' s', Console::FG_RED));
        printf(PHP_EOL . ' --- End [' . date('Y-m-d H:i:s') . '] %s ---' . PHP_EOL . PHP_EOL, $this->ansiFormat(self::class . '\\' . $this->action->id, Console::FG_YELLOW));

    }

    private function cloneClientWithProject($parentClient, $projectId, $typeCreate, $createdDt): void
    {
        $client = Client::create(
            $parentClient['first_name'],
            $parentClient['middle_name'],
            $parentClient['last_name'],
            $projectId,
            $typeCreate,
            $parentClient['id']
        );
        $client->disabled = $parentClient['disabled'];
        $client->cl_type_id = $parentClient['cl_type_id'];
        $client->created = $createdDt;
        $client->is_company = $parentClient['is_company'];
        $client->is_public = $parentClient['is_public'];
        $client->company_name = $parentClient['company_name'];
        $client->detachBehavior('timestamp');
        if (!$client->save()) {
            Yii::error(VarDumper::dumpAsString([
                'errors' => $client->getErrors(),
                'model' => $client->getAttributes(),
            ]), 'OneTimeController:cloneClientWithProject:save');
            return;
        }
        $this->cloneClientPhone($parentClient['id'], $client->id);
        $this->cloneClientEmail($parentClient['id'], $client->id);
    }

    private function cloneClientPhone($fromId, $toId): void
    {
        $oldPhones = ClientPhone::find()->andWhere(['client_id' => $fromId])->asArray()->all();
        foreach ($oldPhones as $oldPhone) {
            $phone = new ClientPhone();
            $phone->enablelAferSave = false;
            $phone->client_id = $toId;
            $phone->phone = $oldPhone['phone'];
            $phone->created = $oldPhone['created'];
            $phone->is_sms = $oldPhone['is_sms'];
            $phone->validate_dt = $oldPhone['validate_dt'];
            $phone->type = $oldPhone['type'] ?: ClientPhone::PHONE_NOT_SET;
            $phone->cp_title = $oldPhone['cp_title'];
            $phone->detachBehavior('timestamp');
            if (!$phone->save()) {
                Yii::error(VarDumper::dumpAsString([
                    'fromId' => $fromId,
                    'toId' => $toId,
                    'errors' => $phone->getErrors(),
                    'model' => $phone->getAttributes(),
                ]), 'OneTimeController:cloneClientPhone:save');
            }
        }
    }

    private function cloneClientEmail($fromId, $toId): void
    {
        $oldEmails = ClientEmail::find()->andWhere(['client_id' => $fromId])->asArray()->all();
        foreach ($oldEmails as $oldEmail) {
            $email = new ClientEmail();
            $email->client_id = $toId;
            $email->email = $oldEmail['email'];
            $email->created = $oldEmail['created'];
            $email->type = $oldEmail['type'] ?: ClientEmail::EMAIL_NOT_SET;
            $email->ce_title = $oldEmail['ce_title'];
            $email->detachBehavior('timestamp');
            if (!$email->save()) {
                Yii::error(VarDumper::dumpAsString([
                    'fromId' => $fromId,
                    'toId' => $toId,
                    'errors' => $email->getErrors(),
                    'model' => $email->getAttributes(),
                ]), 'OneTimeController:cloneClientEmail:save');
            }
        }
    }
}
