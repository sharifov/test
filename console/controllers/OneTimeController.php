<?php

namespace console\controllers;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\components\CheckPhoneByNeutrinoJob;
use common\components\jobs\CreateSaleFromBOJob;
use common\models\Call;
use common\models\CaseSale;
use common\components\jobs\UpdateSaleFromBOJob;
use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Conference;
use common\models\Currency;
use common\models\DepartmentEmailProject;
use common\models\DepartmentPhoneProject;
use common\models\Email;
use common\models\Lead;
use common\models\LeadFlightSegment;
use common\models\LeadFlow;
use common\models\LeadPreferences;
use common\models\PhoneBlacklist;
use common\models\Project;
use common\models\QuotePrice;
use common\models\Sms;
use common\models\Sources;
use common\models\UserProjectParams;
use src\helpers\ErrorsToStringHelper;
use src\model\contactPhoneData\service\ContactPhoneDataDictionary;
use src\entities\cases\Cases;
use src\entities\cases\CasesStatus;
use src\helpers\app\AppHelper;
use src\model\callLog\entity\callLog\CallLog;
use src\model\callLog\entity\callLog\CallLogStatus;
use src\model\callLog\entity\callLog\CallLogType;
use src\model\callLog\entity\callLogLead\CallLogLead;
use src\model\callLog\entity\callLogRecord\CallLogRecord;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChatLead\entity\ClientChatLead;
use src\model\contactPhoneData\entity\ContactPhoneData;
use src\model\contactPhoneData\repository\ContactPhoneDataRepository;
use src\model\contactPhoneData\service\ContactPhoneDataService;
use src\model\contactPhoneList\entity\ContactPhoneList;
use src\model\contactPhoneList\repository\ContactPhoneListRepository;
use src\model\contactPhoneList\service\ContactPhoneListService;
use src\model\contactPhoneList\service\PhoneNumberService;
use src\model\contactPhoneServiceInfo\entity\ContactPhoneServiceInfo;
use src\model\contactPhoneServiceInfo\repository\ContactPhoneServiceInfoRepository;
use src\model\emailList\entity\EmailList;
use src\model\leadUserConversion\entity\LeadUserConversion;
use src\model\phoneList\entity\PhoneList;
use src\repositories\client\ClientEmailRepository;
use src\repositories\client\ClientPhoneRepository;
use src\repositories\client\ClientsQuery;
use src\services\cases\CasesSaleService;
use src\services\client\ClientCreateForm;
use src\services\client\ClientManageService;
use src\services\phone\checkPhone\CheckPhoneNeutrinoService;
use src\services\phone\checkPhone\CheckPhoneService;
use thamtech\uuid\helpers\UuidHelper;
use Yii;
use yii\console\Controller;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseConsole;
use yii\helpers\Console;
use yii\helpers\VarDumper;
use yii\validators\DateValidator;

class OneTimeController extends Controller
{
    private const MC_QUOTE_PRICE_MAX_CHUNK_SIZE_LIMIT = 100000;

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
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;
        $time_start = microtime(true);

        $fromDate = date('Y-m-d', strtotime($fromDate));
        $toDate = date('Y-m-d', strtotime($toDate));
        $processed = $countCases = 0;

        try {
            $cases = $this->getCaseNotSale($fromDate, $toDate, $status);
            $countCases = count($cases);
            Console::startProgress(0, $countCases);

            foreach ($cases as $key => $value) {
                $project = Project::findOne($value['cs_project_id']);
                $job = new CreateSaleFromBOJob();
                $job->case_id = $value['cs_id'];
                $job->phone = $this->getPhoneByClient($value['cs_client_id'])['phone'];
                $job->project_key = $project->api_key ?? null;
                Yii::$app->queue_job->priority(100)->push($job);
                $processed++;
                Console::updateProgress($processed, $countCases);
            }
        } catch (\Throwable $throwable) {
            Yii::error(
                AppHelper::throwableFormatter($throwable),
                'OneTimeController:actionSaleToCase:Throwable'
            );
            echo Console::renderColoredString('%r --- Error : ' . $throwable->getMessage() . ' %n'), PHP_EOL;
        }

        Console::endProgress(false);
        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);
        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time .
            ' s] %gFind cases: %w[' . $countCases . '] %g Added to queue: %w[' . $processed . '] %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . '] %g' . self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;
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
                case_sale.css_sale_id,
                cases.cs_project_id
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
                    $project = Project::findOne($value['cs_project_id']);
                    $job = new CreateSaleFromBOJob();
                    $job->case_id = $value['cs_id'];
                    $job->phone = $this->getPhoneByClient($value['cs_client_id'])['phone'];
                    $job->project_key = $project->api_key ?? null;
                    Yii::$app->queue_job->priority(10)->push($job);
                } else {
                    $job = new UpdateSaleFromBOJob();
                    $job->caseId = $value['cs_id'];
                    $job->saleId = $value['css_sale_id'];
                    Yii::$app->queue_job->priority(10)->push($job);
                }

                $processed++;
                Console::updateProgress($processed, $countItems);
            }
        } catch (\Throwable $throwable) {
            Yii::error(
                AppHelper::throwableFormatter($throwable),
                'OneTimeController:actionSaleToCase:Throwable'
            );
            echo Console::renderColoredString('%r --- Error : ' . $throwable->getMessage() . ' %n'), PHP_EOL;
        }
        Console::endProgress(false);

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);

        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time .
            ' s] %gFind cases: %w[' . $countItems . '] %g Added to queue: %w[' . $processed . '] %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;
    }

    public function actionSaleDataToJson(?string $fromDate = null, ?string $toDate = null): void
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

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
            Yii::error(
                AppHelper::throwableFormatter($throwable),
                'OneTimeController:actionSaleDataToJson:Throwable'
            );
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
                $processed++;
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
                    cases.cs_client_id,
                    cases.cs_project_id
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
            $processed++;
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
            $parentClient['id'],
            null
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
            $phone = ClientPhone::create($oldPhone['phone'], $toId);
            $phone->enablelAferSave = false;
            $phone->created = $oldPhone['created'];
            $phone->is_sms = $oldPhone['is_sms'];
            $phone->validate_dt = $oldPhone['validate_dt'];
            $phone->type = $oldPhone['type'] ?: ClientPhone::PHONE_NOT_SET;
            $phone->cp_title = $oldPhone['cp_title'];
            $phone->detachBehavior('timestamp');
            try {
                $clientPhoneRepository = Yii::createObject(ClientPhoneRepository::class);
                $clientPhoneRepository->save($phone);
            } catch (\RuntimeException $e) {
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
            $email = ClientEmail::create($oldEmail['email'], $toId);
            $email->created = $oldEmail['created'];
            $email->type = $oldEmail['type'] ?: ClientEmail::EMAIL_NOT_SET;
            $email->ce_title = $oldEmail['ce_title'];
            $email->detachBehavior('timestamp');

            try {
                $clientPhoneRepository = Yii::createObject(ClientEmailRepository::class);
                $clientPhoneRepository->save($email);
            } catch (\RuntimeException $e) {
                Yii::error(VarDumper::dumpAsString([
                    'fromId' => $fromId,
                    'toId' => $toId,
                    'errors' => $email->getErrors(),
                    'model' => $email->getAttributes(),
                ]), 'OneTimeController:cloneClientEmail:save');
            }
        }
    }

    public function actionAddLeadViewBlockPermissions()
    {
        $auth = Yii::$app->authManager;

        foreach ($auth->getRoles() as $role) {
            foreach ($auth->getPermissionsByRole($role->name) as $permission) {
                if ($permission->name === '/leads/index') {
                    $this->addNewPermissions($role);
                }
            }
        }
    }

    private function addNewPermissions($role): void
    {
        $permissions = [
            [
                'lead-view/client-info/view',
                'lead-view/client-info/view/all',
                'lead-view/client-info/view/owner',
                'lead-view/client-info/view/empty',
                'lead-view/client-info/view/group',
            ],
            [
                'lead-view/lead-preferences/view',
                'lead-view/lead-preferences/view/all',
                'lead-view/lead-preferences/view/owner',
                'lead-view/lead-preferences/view/empty',
                'lead-view/lead-preferences/view/group',
            ],
            [
                'lead-view/check-list/view',
                'lead-view/check-list/view/all',
                'lead-view/check-list/view/owner',
                'lead-view/check-list/view/empty',
                'lead-view/check-list/view/group',
            ],
            [
                'lead-view/task-list/view',
                'lead-view/task-list/view/all',
                'lead-view/task-list/view/owner',
                'lead-view/task-list/view/empty',
                'lead-view/task-list/view/group',
            ],
            [
                'lead-view/communication-block/view',
                'lead-view/communication-block/view/all',
                'lead-view/communication-block/view/owner',
                'lead-view/communication-block/view/empty',
                'lead-view/communication-block/view/group',
            ],
            [
                'lead-view/call-expert/view',
                'lead-view/call-expert/view/all',
                'lead-view/call-expert/view/owner',
                'lead-view/call-expert/view/empty',
                'lead-view/call-expert/view/group',
            ],
            [
                'lead-view/notes/view',
                'lead-view/notes/view/all',
                'lead-view/notes/view/owner',
                'lead-view/notes/view/empty',
                'lead-view/notes/view/group',
            ],
            [
                'lead-view/flight-default/view',
                'lead-view/flight-default/view/all',
                'lead-view/flight-default/view/owner',
                'lead-view/flight-default/view/empty',
                'lead-view/flight-default/view/group',
            ]
        ];
        foreach ($permissions as $group) {
            $this->assignPermissions($role, $group);
        }
    }

    private function assignPermissions($role, $permissions): void
    {
        $auth = Yii::$app->authManager;
        foreach ($permissions as $permission) {
            if ($p = $auth->getPermission($permission)) {
                if (!$auth->hasChild($role, $p)) {
                    $auth->addChild($role, $p);
                }
            }
        }
    }

    public function actionFixSaleSegments(): void
    {
        $offset = BaseConsole::input('Start from: ');
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

        $query = CaseSale::find()
            ->offset($offset)
            ->limit($limit)
            ->orderBy(['css_cs_id' => SORT_ASC]);

        $count = CaseSale::find()->count();

        Console::startProgress(0, $count);
        $processed = 0;

        $errors = [];
        $saleService = Yii::createObject(CasesSaleService::class);
        foreach ($query->each(20) as $sale) {
            $processed++;
            Console::updateProgress($processed, $count);
            $saleData = $sale->getSaleDataDecoded();
            if (!$saleData) {
                continue;
            }
            $saleService->prepareSegmentsData($sale, $saleData);
            if (!$sale->save()) {
                $errors[] = [
                    'saleCaseId' => $sale->css_cs_id,
                    'errors' => $sale->getErrors(),
                ];
            }
        }

        if ($errors) {
            echo 'Errors: ' . PHP_EOL;
            VarDumper::dump($errors);
        }

        Console::endProgress(false);

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);

        printf(PHP_EOL . 'Execute Time: %s' . PHP_EOL, $this->ansiFormat($time . ' s', Console::FG_RED));
        printf(PHP_EOL . ' --- End [' . date('Y-m-d H:i:s') . '] %s ---' . PHP_EOL . PHP_EOL, $this->ansiFormat(self::class . '\\' . $this->action->id, Console::FG_YELLOW));
    }

    public function actionFixOldCallLogEntities()
    {
        $dateValidator = new DateValidator([
            'format' => 'php:Y-m-d'
        ]);

        $from = BaseConsole::input('Date from: ');

        if (!$dateValidator->validate($from, $error)) {
            echo 'Date From error: ' . $error . PHP_EOL;
            return;
        }

        $to = BaseConsole::input('Date to: ');

        if (!$dateValidator->validate($to, $error)) {
            echo 'Date To error: ' . $error . PHP_EOL;
            return;
        }

        $clientManageService = Yii::createObject(ClientManageService::class);

        $logs = CallLog::find()->alias('log')
            ->innerJoin(Client::tableName() . ' as client', 'client.id = log.cl_client_id')
            ->andWhere(['>=', 'log.cl_call_created_dt', $from])
            ->andWhere(['<', 'log.cl_call_created_dt', $to])
            ->andWhere(new Expression('log.cl_id = log.cl_group_id'))
            ->andWhere(['log.cl_type_id' => Call::CALL_TYPE_IN])
            ->andWhere(new Expression('log.cl_project_id != client.cl_project_id'))
            ->with(['case.client', 'lead.client'])
            ->all();

        $count = count($logs);

        Console::startProgress(0, $count);
        $processed = 0;

        foreach ($logs as $log) {
            $clientId = null;
            if ($log->lead && $log->lead->client_id && $log->lead->client->cl_project_id === $log->cl_project_id) {
                $clientId = $log->lead->client_id;
            } elseif ($log->case && $log->case->cs_client_id && $log->case->client->cl_project_id === $log->cl_project_id) {
                $clientId = $log->case->cs_client_id;
            } else {
                $client = ClientsQuery::oneByPhoneAndProject($log->cl_phone_from, $log->cl_project_id, null);
                if ($client) {
                    $clientId = $client->id;
                } else {
                    $clientForm = ClientCreateForm::createWidthDefaultName();
                    $clientForm->projectId = $log->cl_project_id;
                    $clientForm->typeCreate = Client::TYPE_CREATE_CALL;
                    $client = $clientManageService->create($clientForm, null);
                    $clientId = $client->id;
                }
            }
            CallLog::updateAll(['cl_client_id' => $clientId], ['cl_group_id' => $log->cl_group_id]);
            $processed++;
            Console::updateProgress($processed, $count);
        }
        Console::endProgress(false);
    }

    public function actionLeadUserConversion(): void
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

        $processed = 0;
        $time_start = microtime(true);
        $fromDate = date("Y-m-01");
        $toDate = date("Y-m-d");

        $result = [];

        try {
            $db = \Yii::$app->db;
            //$db->createCommand()->truncateTable(LeadUserConversion::tableName())->execute();

            $sql = '
                select 
                    lead_id as result_lead_id,
                    lf_owner_id as result_user_id,
                    case
                        when lf_from_status_id = 1 and lead_flow.status = 2 and lead_flow.employee_id = lf_owner_id then "Take"
                        when lf_from_status_id = 1 and lead_flow.status = 2 and lead_flow.employee_id is null then "Call Auto Take"
                        when lf_from_status_id is null and lead_flow.status = 2 and l.clone_id is not null and ll.employee_id != lf_owner_id then "Clone"
                        when lf_from_status_id is null and lead_flow.status = 2 and l.clone_id is null then "Manual"
                        when lf_from_status_id = 2 and lead_flow.status = 2 and lead_flow.employee_id = lf_owner_id then "Take Over"
                    else null end as result_description,
                    min(lead_flow.created) as result_created_dt
                from lead_flow
                left join leads l on l.id = lead_flow.lead_id
                left join leads ll on ll.id = l.clone_id 
                
                LEFT JOIN lead_user_conversion 
                    ON lead_user_conversion.luc_lead_id = lead_flow.lead_id AND lead_user_conversion.luc_user_id = lf_owner_id
                
                where 
                    lead_flow.created >= "2021-05-01 00:00:00" and lead_flow.created < "2021-06-21 09:46:00" 
                    
                    AND lead_user_conversion.luc_lead_id IS NULL
                    
                    and
                    (
                        (lf_from_status_id = 1 and lead_flow.status = 2 and (lead_flow.employee_id = lf_owner_id or lead_flow.employee_id is null))
                        OR
                        (lf_from_status_id is null and lead_flow.status = 2 and (ll.employee_id != lf_owner_id or l.clone_id is null) )
                        OR
                        (lf_from_status_id = 2 and lead_flow.status = 2 and lead_flow.employee_id = lf_owner_id)
                    )
                group by result_lead_id, result_user_id, result_description';

            $result = Yii::$app->db->createCommand($sql)->queryAll();

            /*$processed = $db->createCommand()
                ->batchInsert(
                    LeadUserConversion::tableName(),
                    ['luc_lead_id', 'luc_user_id', 'luc_description', 'luc_created_dt'],
                    $result
                )->execute();*/
        } catch (\Throwable $throwable) {
            Yii::error(
                AppHelper::throwableFormatter($throwable),
                'OneTimeController:actionLeadUserConversion:Throwable'
            );
            echo Console::renderColoredString('%r --- Error : ' . $throwable->getMessage() . ' %n'), PHP_EOL;
        }

        foreach ($result as $value) {
            $leadUserConversion = new LeadUserConversion();
            $leadUserConversion->luc_lead_id = $value['result_lead_id'];
            $leadUserConversion->luc_user_id = $value['result_user_id'];
            $leadUserConversion->luc_description = $value['result_description'];
            $leadUserConversion->luc_created_dt = $value['result_created_dt'];

            if (!$leadUserConversion->save()) {
                echo Console::renderColoredString('%r --- Not save : ' . ErrorsToStringHelper::extractFromModel($leadUserConversion) . ' %n'), PHP_EOL;
                continue;
            }
        }

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);
        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time . ' s] %g Processed: %w[' . $processed . '] %g %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;
    }

    public function actionPhoneList(): void
    {
        $offset = BaseConsole::input('Offset: ');
        $limit = BaseConsole::input('Limit: ');

        $offset = (int) $offset;
        $limit = (int) $limit;

        if ($offset < 1) {
            echo 'Start must be > 0' . PHP_EOL;
            return;
        }
        if ($limit < 1) {
            echo 'Limit must be > 0' . PHP_EOL;
            return;
        }
        $offset--;

        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

        $time_start = microtime(true);

        $query = (new Query())
            ->select(['phone'])
            ->from(ClientPhone::tableName())
            ->where('cp_cpl_uid IS NULL')
            ->offset($offset)
            ->limit($limit)
            ->groupBy(['phone']);

        $count = $query->count();
        $validator = new PhoneInputValidator();
        $processed = 0;
        $errors = [];
        $notValidPhones = [];
        $batchSize = $limit > 25 ? $limit : 25;

        Console::startProgress(0, $count);

        foreach ($query->each($batchSize) as $clientPhone) {
            try {
                if (!$validator->validate($clientPhone['phone'])) {
                    $notValidPhones[] = $clientPhone['phone'];
                    \Yii::info(
                        'Phone(' . $clientPhone['phone'] . ') not valid',
                        'info\OneTimeController:actionPhoneList:notValid'
                    );
                    continue;
                }

                $job = new CheckPhoneByNeutrinoJob();
                $job->phone = $clientPhone['phone'];
                $job->title = 'trust';
                Yii::$app->queue_phone_check->priority(10)->push($job);

                $processed++;
                Console::updateProgress($processed, $count);
            } catch (\Throwable $throwable) {
                Yii::error(AppHelper::throwableLog($throwable), 'OneTimeController:actionPhoneList:Throwable');
                $errors[] = [
                    'phone' => $clientPhone['phone'],
                    'message' => $throwable->getMessage()
                ];
            }
        }

        Console::endProgress(false);

        if ($notValidPhones) {
            echo Console::renderColoredString('%r --- NotValidPhones count(' . count($notValidPhones) . '). Please see logs. %n'), PHP_EOL;
        }
        if ($errors) {
            echo Console::renderColoredString('%r --- Errors count(' . count($errors) . '). Please see logs. %n'), PHP_EOL;
        }

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);
        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time . ' s] %g %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- Processed: %w[' . $processed . '/' . $count . '] %g %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;
    }

    public function actionFillTrustPhone(): void
    {
        $offset = BaseConsole::input('Offset: ');
        $limit = BaseConsole::input('Limit: ');
        $truncate = BaseConsole::input('Truncate table? (0/1): ');

        $offset = (int) $offset;
        $limit = (int) $limit;
        $truncate = (int) $truncate;

        if ($offset < 1) {
            echo 'Start must be > 0' . PHP_EOL;
            return;
        }
        if ($limit < 1) {
            echo 'Limit must be > 0' . PHP_EOL;
            return;
        }
        if (!ArrayHelper::isIn($truncate, [0, 1], true)) {
            echo 'Truncate must be 0 or 1' . PHP_EOL;
            return;
        }
        $offset--;

        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

        if ($truncate) {
            Yii::$app->db->createCommand()->truncateTable(ContactPhoneData::tableName())->execute();
        }

        $time_start = microtime(true);

        $query = (new Query())
            ->select([ClientPhone::tableName() . '.phone'])
            ->from(ClientPhone::tableName())
            ->innerJoin(Client::tableName(), ClientPhone::tableName() . '.client_id = ' . Client::tableName() . '.id')
            ->innerJoin([
                'sold_lead_clients' => Lead::find()
                    ->select(['client_id'])
                    ->where(['IN', 'status', [Lead::STATUS_SOLD, Lead::STATUS_BOOKED]])
                    ->groupBy(['client_id'])
            ], Client::tableName() . '.id = sold_lead_clients.client_id')
            ->leftJoin([
                'exist_phone_data' => ContactPhoneList::find()
                    ->select(['cpl_phone_number'])
                    ->innerJoin(ContactPhoneData::tableName(), 'cpl_id = cpd_cpl_id')
                    ->where(['cpd_key' => ContactPhoneDataDictionary::KEY_IS_TRUSTED])
                    ->groupBy(['cpl_phone_number'])
            ], ClientPhone::tableName() . '.phone = exist_phone_data.cpl_phone_number')
            ->leftJoin(PhoneBlacklist::tableName(), ClientPhone::tableName() . '.phone = pbl_phone')
            ->where(['!=', ClientPhone::tableName() . '.type', ClientPhone::PHONE_VALID])
            ->andWhere('exist_phone_data.cpl_phone_number IS NULL')
            ->andWhere('pbl_phone IS NULL')
            ->offset($offset)
            ->limit($limit)
            ->groupBy([ClientPhone::tableName() . '.phone']);

        $count = $query->count();
        $validator = new PhoneInputValidator();
        $processed = 0;
        $errors = [];
        $notValidPhones = [];
        $batchSize = $limit > 25 ? $limit : 25;

        Console::startProgress(0, $count);

        foreach ($query->each($batchSize) as $clientPhone) {
            try {
                if (!$validator->validate($clientPhone['phone'])) {
                    $notValidPhones[] = $clientPhone['phone'];
                    \Yii::info(
                        'Phone(' . $clientPhone['phone'] . ') not valid',
                        'info\OneTimeController:actionFillTrustPhone:notValid'
                    );
                    continue;
                }

                $contactPhoneList = ContactPhoneListService::getOrCreate($clientPhone['phone'], 'Is trusted');
                ContactPhoneDataService::getOrCreate(
                    $contactPhoneList->cpl_id,
                    ContactPhoneDataDictionary::KEY_IS_TRUSTED,
                    '1'
                );

                $processed++;
                Console::updateProgress($processed, $count);
            } catch (\Throwable $throwable) {
                $throwableData = AppHelper::throwableLog($throwable);
                $throwableData['phone'] = $clientPhone['phone'];
                Yii::error($throwableData, 'OneTimeController:actionFillTrustPhone:Throwable');
                $errors[] = [
                    'phone' => $clientPhone['phone'],
                    'message' => $throwable->getMessage()
                ];
            }
        }

        Console::endProgress(false);

        if ($notValidPhones) {
            echo Console::renderColoredString('%r --- NotValidPhones count(' . count($notValidPhones) . '). Please see logs. %n'), PHP_EOL;
        }
        if ($errors) {
            echo Console::renderColoredString('%r --- Errors count(' . count($errors) . '). Please see logs. %n'), PHP_EOL;
        }

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);
        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time . ' s] %g %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- Processed: %w[' . $processed . '/' . $count . '] %g %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;
    }

    public function actionSaveLeadsForRecoverySource()
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

        $leads = $this->getLeadsForRecoverySources();
        Yii::warning([
            'leads' => array_column($leads, 'id'),
        ], 'RecoverySourceLeads');

        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;
    }

    public function actionRecoveryLeadSource()
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

        $processed = 0;
        $time_start = microtime(true);

        $leads = $this->getLeadsForRecoverySources();

        $count = count($leads);
        Console::startProgress(0, $count);

        $defaultSources = [];
        foreach (Project::find()->all() as $project) {
            $source = Sources::getByProjectId($project->id);
            if ($source) {
                $defaultSources[(int)$project->id] = $source->id;
            }
        }

        Yii::warning([
            'message' => 'Found ' . $count . ' leads for update source'
        ], 'RecoverySourceLeads');

        $countUpdated = 0;
        foreach ($leads as $lead) {
            $sourceId = $defaultSources[(int)$lead['project_id']] ?? null;
            if ($sourceId) {
                try {
                    Lead::updateAll(['source_id' => $sourceId], 'id = ' . (int)$lead['id']);
                    $countUpdated++;
                } catch (\Throwable $e) {
                    Yii::error([
                        'message' => $e->getMessage(),
                        'leadId' => $lead['id'],
                    ], 'RecoverySourceLeads');
                }
            } else {
                Yii::error([
                    'message' => 'Not found Source',
                    'leadId' => $lead['id'],
                    'projectId' => $lead['project_id'],
                ], 'RecoverySourceLeads');
            }
            $processed++;
            Console::updateProgress($processed, $count);
        }

        Yii::warning([
            'message' => $countUpdated . ' from ' . $count . ' leads updated source',
        ], 'RecoverySourceLeads');

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);
        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time . ' s] %g Processed: %w[' . $processed . '] %g %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;
    }

    public function actionFillContactPhoneList(): void
    {
        $offset = BaseConsole::input('Offset (if "0" - w/o): ');
        $limit = BaseConsole::input('Limit (if "0" - w/o): ');

        $offset = (int) $offset;
        $limit = (int) $limit;

        if ($offset < 0) {
            echo 'Start must be >= 0' . PHP_EOL;
            return;
        }
        if ($limit < 0) {
            echo 'Limit must be >= 0' . PHP_EOL;
            return;
        }

        $offset--;
        $time_start = microtime(true);

        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

        ClientPhone::updateAll(
            ['cp_cpl_uid' => null, 'cp_cpl_id' => null],
        );

        $query = ClientPhone::find()
            ->select([ClientPhone::tableName() . '.phone'])
            ->groupBy([ClientPhone::tableName() . '.phone'])
            ->orderBy([ClientPhone::tableName() . '.phone' => SORT_ASC]);

        if ($offset !== 0) {
            $query->offset($offset);
        }
        if ($limit !== 0) {
            $query->limit($limit);
        }

        $count = $query->count();
        $validator = new PhoneInputValidator();
        $processed = 0;
        $errors = $warnings = $notValidPhones = [];
        $batchSize = max($limit, 100);

        Console::startProgress(0, $count);

        foreach ($query->each($batchSize) as $clientPhone) {
            $phoneNumberService = new PhoneNumberService((string) $clientPhone['phone']);
            if (!$validator->validate($phoneNumberService->getCleanedPhoneNumber())) {
                $notValidPhones[] = $phoneNumberService->getCleanedPhoneNumber();
                continue;
            }

            try {
                if (!$contactPhoneList = ContactPhoneListService::getByPhone($phoneNumberService->getCleanedPhoneNumber())) {
                    $contactPhoneList = ContactPhoneList::create($clientPhone['phone']);
                    if (!$contactPhoneList->validate()) {
                        throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($contactPhoneList));
                    }
                    (new ContactPhoneListRepository())->save($contactPhoneList);
                }

                ClientPhone::updateAll(
                    ['cp_cpl_uid' => $contactPhoneList->cpl_uid, 'cp_cpl_id' => $contactPhoneList->cpl_id],
                    ['phone' => $clientPhone['phone']]
                );

                $processed++;
                Console::updateProgress($processed, $count);
            } catch (\RuntimeException | \DomainException $throwable) {
                $warnings[] = [
                    'message' => $throwable->getMessage(),
                    'phone' => $clientPhone['phone'],
                    'cleanedPhone' => $phoneNumberService->getCleanedPhoneNumber(),
                    'uid' => $phoneNumberService->getUid(),
                ];
            } catch (\Throwable $throwable) {
                $errors[] = [
                    'message' => $throwable->getMessage(),
                    'phone' => $clientPhone['phone'],
                    'cleanedPhone' => $phoneNumberService->getCleanedPhoneNumber(),
                    'uid' => $phoneNumberService->getUid(),
                ];
            }
        }

        Console::endProgress(false);

        if ($notValidPhones) {
            echo Console::renderColoredString('%r --- NotValidPhones count(' . count($notValidPhones) . '). %n'), PHP_EOL;
        }
        if ($warnings) {
            \Yii::info($warnings, 'info\OneTimeController:actionFillContactPhoneList:Warnings');
            echo Console::renderColoredString('%r --- Warnings count(' . count($warnings) . '). Please see logs. %n'), PHP_EOL;
        }
        if ($errors) {
            \Yii::info($errors, 'info\OneTimeController:actionFillContactPhoneList:Errors');
            echo Console::renderColoredString('%r --- Errors count(' . count($errors) . '). Please see logs. %n'), PHP_EOL;
        }

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);
        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time . ' s] %g %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- Processed: %w[' . $processed . '/' . $count . '] %g %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;
    }

    private function getLeadsForRecoverySources()
    {
        return (new Query())
            ->select(['leads.id', 'leads.project_id'])
            ->from('leads')
            ->leftJoin('projects', 'projects.id = leads.project_id')
            ->leftJoin('sources', 'sources.id = leads.source_id')
            ->andWhere([
                'OR',
                'leads.project_id != sources.project_id',
                'leads.source_id is null'
            ])
            ->all();
    }

    public function actionFillClientPriceFieldsInQuotePrice(int $chunkSize = self::MC_QUOTE_PRICE_MAX_CHUNK_SIZE_LIMIT): void
    {
        try {
            if ($chunkSize > self::MC_QUOTE_PRICE_MAX_CHUNK_SIZE_LIMIT) {
                throw new \DomainException('max chunk size is exceeded - Chunk size is:' . $chunkSize);
            }
            $timeStart = microtime(true);
            $quotePriceIds = QuotePrice
                ::find()
                ->select('id')
                ->where(['is', 'qp_client_fare', new Expression('null')])
                ->limit($chunkSize)
                ->orderBy(['id' => SORT_ASC])
                ->column();
            $quotePriceTable = QuotePrice::tableName();
            \Yii::$app->db->createCommand()
                ->update($quotePriceTable, [
                    'qp_client_fare' => new Expression('fare'),
                    'qp_client_taxes' => new Expression('taxes'),
                    'qp_client_extra_mark_up' => new Expression('extra_mark_up'),
                    'qp_client_service_fee' => new Expression('service_fee'),
                    'qp_client_markup' => new Expression('mark_up'),
                    'qp_client_selling' => new Expression('selling'),
                    'qp_client_net' => new Expression('net'),
                ], ['id' => $quotePriceIds])
                ->execute();
            $timeEnd = microtime(true);
            $executionTime = ($timeEnd - $timeStart) / 60;
            $message = ['message' => 'actionFillClientPriceFieldsInQuotePrice finished','countAffectedRows' =>  count($quotePriceIds), 'executionTime' => $executionTime ];
            \Yii::info(
                $message,
                'info\OneTimeController::actionFillClientPriceFieldsInQuotePrice'
            );
        } catch (\RuntimeException | \DomainException $e) {
            \Yii::warning(
                AppHelper::throwableLog($e),
                'OneTimeController::actionFillClientPriceFieldsInQuotePrice:exception'
            );
        } catch (\Throwable $e) {
            \Yii::error(
                AppHelper::throwableLog($e),
                'OneTimeController:actionFillClientPriceFieldsInQuotePrice:Throwable'
            );
        }
    }

    public function actionFillDefaultCurrencyInLeadPreferences()
    {
        try {
            $timeStart = microtime(true);
            $chunkSize = 10000;
            $preferencesTableName = LeadPreferences::tableName();
            $leads = Lead::find()
                         ->leftJoin($preferencesTableName, $preferencesTableName . '.lead_id = leads.id')
                         ->where(['is', $preferencesTableName . '.lead_id', new Expression('null')])
                         ->orWhere(['is', $preferencesTableName . '.pref_currency', new Expression('null')])
                         ->limit($chunkSize)
                         ->all();
            $data = ArrayHelper::toArray($leads, [
                'common\models\Lead' => [
                    'id',
                    'hasPreferences'     => function ($lead) {
                        return isset($lead->leadPreferences);
                    },
                    'hasSettledCurrency' => function ($lead) {
                        return isset($lead->leadPreferences->pref_currency);
                    },
                ],
            ]);
            $leadsWithoutPreferences = array_filter($data, function ($item) {
                return !$item['hasPreferences'];
            });
            array_walk($leadsWithoutPreferences, function (&$item) {
                $item['lead_id'] = $item['id'];
                unset($item['id']);
                unset($item['hasPreferences']);
                unset($item['hasSettledCurrency']);
                $item['pref_currency'] = Currency::DEFAULT_CURRENCY;
            });
            $leadsWithoutDefaultCurrency  = array_filter($data, function ($item) {
                return $item['hasPreferences'] && !$item['hasSettledCurrency'];
            });
            $leadsWithoutDefaultCurrency = ArrayHelper::getColumn($leadsWithoutDefaultCurrency, 'id');
            Yii::$app
                ->db
                ->createCommand()
                ->batchInsert($preferencesTableName, ['lead_id', 'pref_currency'], $leadsWithoutPreferences)
                ->execute();
            Yii::$app
                ->db
                ->createCommand()
                ->update($preferencesTableName, [
                    'pref_currency' => Currency::DEFAULT_CURRENCY,
                ], ['lead_id' => $leadsWithoutDefaultCurrency])
                ->execute();
            $timeEnd = microtime(true);
            $executionTime = ($timeEnd - $timeStart) / 60;
            $message = ['message' => 'actionFillClientPriceFieldsInQuotePrice finished','countAffectedRows' =>  count($leads), 'executionTime' => $executionTime ];
            \Yii::info(
                $message,
                'info\OneTimeController::actionFillDefaultCurrencyInLeadPreferences'
            );
        } catch (\RuntimeException | \DomainException $e) {
            \Yii::warning(
                AppHelper::throwableLog($e),
                'OneTimeController::actionFillDefaultCurrencyInLeadPreferences:exception'
            );
        } catch (\Throwable $e) {
            \Yii::error(
                AppHelper::throwableLog($e),
                'OneTimeController:actionFillDefaultCurrencyInLeadPreferences:Throwable'
            );
        }
    }
}
