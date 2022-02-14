<?php

namespace console\controllers;

use common\models\Call;
use common\models\Lead;
use common\models\LeadFlightSegment;
use common\models\LeadFlow;
use common\models\LeadQcall;
use common\models\Task;
use modules\featureFlag\FFlag;
use src\exception\BoResponseException;
use src\helpers\app\AppHelper;
use src\helpers\setting\SettingHelper;
use src\model\leadPoorProcessing\entity\LeadPoorProcessing;
use src\model\leadPoorProcessing\service\LeadPoorProcessingChecker;
use src\model\leadPoorProcessing\service\LeadPoorProcessingService;
use src\model\leadPoorProcessing\service\LeadToExtraQueueService;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataDictionary;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataQuery;
use src\model\leadPoorProcessingData\service\scheduledCommunication\ScheduledCommunicationService;
use src\model\leadUserData\entity\LeadUserData;
use src\model\leadUserData\entity\LeadUserDataDictionary;
use src\repositories\lead\LeadRepository;
use src\services\cases\CasesSaleService;
use yii\base\InvalidArgumentException;
use yii\console\Controller;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\VarDumper;
use Yii;
use yii\validators\Validator;

/**
 * Class LeadController
 *
 * @property LeadRepository $leadRepository
 */
class LeadController extends Controller
{
    private $leadRepository;

    public function __construct($id, $module, LeadRepository $leadRepository, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->leadRepository = $leadRepository;
    }

    public function actionReturnLeadToReady(): void
    {
        $report = [];

        $interval = new \DateInterval('PT1M');
        $interval->invert = 1;
        $from = (new \DateTimeImmutable())->add($interval);

        $leads = Lead::find()
            ->andWhere(['l_call_status_id' => Lead::CALL_STATUS_PREPARE])
            ->andWhere(['<', 'l_last_action_dt', $from->format('Y-m-d H:i:s')])
            ->all();

        foreach ($leads as $lead) {
            try {
                /** @var Lead $lead */
                $lead->callReady();
                $this->leadRepository->save($lead);
                $report[$lead->id] = 'Lead: ' . $lead->id . ' -> callReady';
            } catch (\Throwable $e) {
                $report[] = 'Lead: ' . $lead->id . ' not updated';
                Yii::error($e, 'Lead:ReturnToCallReadyStatus');
            }
        }
        foreach ($report as $item) {
            echo $item . PHP_EOL;
        }

        $message = '0 leads returned to call ready';
        if (count($report) > 0) {
            $message = count($report) . ' leads returned to call ready. [' . implode(', ', array_keys($report)) . ']';
        }

        //Yii::info($message, 'info\CronReturnLead');

        //**********

        $report = [];

        foreach ($this->getBuggedLeads() as $lead) {
            try {
                /** @var Lead $lead */
                $lead->callBugged();
                $this->leadRepository->save($lead);
                $report[$lead->id] = 'Lead: ' . $lead->id . ' -> callBugged';
            } catch (\Throwable $e) {
                $report[] = 'Lead: ' . $lead->id . ' not updated';
                Yii::error($e, 'Lead:ReturnToCallBuggedStatus');
            }
        }

        foreach ($report as $item) {
            echo $item . PHP_EOL;
        }

        //$message = '0 leads returned to call bugged';
        if (count($report) > 0) {
            $message = count($report) . ' leads returned to call bugged. [' . implode(', ', array_keys($report)) . ']';
            Yii::info($message, 'info\CronReturnLead');
        }
    }

    /**
     * @return array Leads
     */
    private function getBuggedLeads(): array
    {
        $countRedialCalls = (int)Yii::$app->params['settings']['redial_calls_bugged'];
        $leads = Lead::find()->select('*')
            ->addSelect(['last_lead_flow_count' =>
                (new Query())->select(['lf_out_calls'])
                    ->from(LeadFlow::tableName())
                    ->andWhere(LeadFlow::tableName() . '.lead_id = ' . Lead::tableName() . '.id')
                    ->orderBy([LeadFlow::tableName() . '.created' => SORT_DESC])
                    ->limit(1)
            ])
            ->addSelect(['count_redial_calls' =>
                (new Query())->select('count(*)')
                    ->from(Call::tableName())
                    ->andWhere(['c_source_type_id' => Call::SOURCE_REDIAL_CALL])
                    ->andWhere(Call::tableName() . '.c_lead_id = ' . Lead::tableName() . '.id')
                    ->andWhere(LeadQcall::tableName() . '.lqc_created_dt <= ' . Call::tableName() . '.c_created_dt')
            ])
            ->innerJoin(LeadQcall::tableName(), Lead::tableName() . '.id = ' . LeadQcall::tableName() . '.lqc_lead_id')
            ->andWhere(['<>', 'l_call_status_id', Lead::CALL_STATUS_BUGGED])
            ->andHaving(['last_lead_flow_count' => 0])
            ->andHaving(['>', 'count_redial_calls', $countRedialCalls])
//            ->createCommand()->getRawSql();
            ->all();
        return $leads;
    }

    /**
     *
     */
    public function actionUpdateIpInfo(): void
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        $leads = Lead::find()
            ->where(['OR', ['offset_gmt' => null], ['offset_gmt' => '']])
            ->andWhere(['status' => [Lead::STATUS_PENDING, Lead::STATUS_PROCESSING]])
            //->andWhere(['OR', ['IS NOT', 'request_ip', null], ['request_ip' => '']])
            ->orderBy(['id' => SORT_DESC])
            ->limit(50)->all();

        //print_r($leads->createCommand()->getRawSql());

        if ($leads) {
            foreach ($leads as $lead) {
                if ($lead->request_ip) {
                    sleep(1);
                }

                $out = $lead->updateIpInfo();

                if (!empty($out['error'])) {
                    echo $lead->id . "\r\n";
                    VarDumper::dump($out);
                } else {
                    echo $lead->id . ' -  offset_gmt: ' . $lead->offset_gmt . ' -  ip: ' . $lead->request_ip . ' - OK - ';
                    if (isset($out['data']['timeZone'])) {
                        VarDumper::dump($out['data']['timeZone']);
                    }
                }
                echo "\r\n";
            }
        }
        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }



    /*public function actionEmail()
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        $swiftMailer = \Yii::$app->mailer2;
        $body = 'Hi! My name is '.date('Y-m-d H:i:s');
        $subject = '✈ ⚠ [Sales] Default subject';

        try {
            $isSend = $swiftMailer
                ->compose()//'sendDeliveryEmailForClient', ['order' => $this])
                //->setTo(['alex.connor@techork.com', 'ac@zeit.style'])
                ->setTo('ac@zeit.style')
                ->setBcc(['alex.connor@techork.com'])
                ->setFrom(\Yii::$app->params['email_from']['sales'])
                ->setSubject($subject)
                ->setTextBody($body)
                ->send();
            if($isSend) {
                echo ' - Send';
            }

        } catch (\Throwable $e) {
            print_r($e->getMessage());
        }
        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }*/


    /**
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     *
     * sudo crontab -e
     * "10   0  *  *  *     run-this-one php /var/www/sale/yii lead/update-by-tasks"
     */


    public function actionUpdateByTasks(): void
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        /* $swiftMailer = \Yii::$app->mailer2;
         $body = 'Hi! '.date('Y-m-d H:i:s');
         $subject = '✈ ⚠ [Sales] Default subject';

         try {
             $isSend = $swiftMailer
                 ->compose()//'sendDeliveryEmailForClient', ['order' => $this])
                 //->setTo(['alex.connor@techork.com', 'ac@zeit.style'])
                 ->setTo('ac@zeit.style')
                 ->setBcc(['alex.connor@techork.com'])
                 ->setFrom(\Yii::$app->params['email_from']['sales'])
                 ->setSubject($subject)
                 ->setTextBody($body)
                 ->send();
             if($isSend) {
                 echo ' - Send';
             }

         } catch (\Throwable $e) {
             print_r($e->getMessage());
         }*/


        /*
         'lt_lead_id' => '12008'
         'lt_user_id' => '1'
         'status' => '8'
         'l_answered' => '1'
         't_category_id' => '2'
         'checked_cnt' => '1'
         'all_cnt' => '4'
         'last_task_date' => '2018-10-06'
        */

        $repo = Yii::createObject(LeadRepository::class);

        $leadsData = Lead::getEndTaskLeads(Task::CAT_NOT_ANSWERED_PROCESS);

        echo " ---- TASK CATEGORY 1 ----\n";
        if ($leadsData) {
            foreach ($leadsData as $leadItem) {
                $lead = Lead::findOne($leadItem['lt_lead_id']);
                if ($lead) {
                    /*if(!$leadItem['l_answered']) {
                       $lead->status = Lead::STATUS_SNOOZE;
                       $lead->status_description = 'System Autochange status to SNOOZE ('.$leadItem['checked_cnt'] .'/'. $leadItem['all_cnt'] . ' tasks completed)';
                    } else*/

                    if (!$leadItem['l_answered']) {
                        try {
                            $lead->followUp($lead->employee_id, null, 'System AutoChange status to FOLLOW UP');
                            $repo->save($lead);
                        } catch (\Throwable $e) {
                            Yii::error($e->getMessage(), 'ConsoleLeadController:UpdateByTask:FollowUp');
                        }
                        //    status_description - Was deleted
 //                       $lead->status_description = 'System AutoChange status to FOLLOW_UP ('.$leadItem['checked_cnt'] .'/'. $leadItem['all_cnt'] . ' tasks completed)';
 //                       if ($lead->employee_id && !$lead->sendNotification('lead-status-follow-up', $lead->employee_id, null, $lead)) {
 //                           Yii::warning('Not send Email notification to employee_id: ' . $lead->employee_id . ', lead: ' . $lead->id, 'Console:LeadController:UpdateByTasks:sendNotification');
 //                       }
                    }

                    echo ' - Lead: ' . $lead->id . ' - ' . $leadItem['checked_cnt'] . '/' . $leadItem['all_cnt'] . " tasks completed \r\n";
                }
            }
        }


        $leadsData = Lead::getEndTaskLeads(Task::CAT_ANSWERED_PROCESS);
        echo " ---- TASK CATEGORY 2 ----\n";

        if ($leadsData) {
            foreach ($leadsData as $leadItem) {
                $lead = Lead::findOne($leadItem['lt_lead_id']);
                if ($lead) {
                    if ($leadItem['l_answered']) {
//                        $lead->status = Lead::STATUS_SNOOZE;
//                        $lead->snooze_for = date('Y-m-d', strtotime('+3 days'));

                        try {
                            $lead->snooze(date('Y-m-d', strtotime('+3 days')), $lead->employee_id, null, 'System AutoChange status to SNOOZE');
                            $repo->save($lead);
                        } catch (\Throwable $e) {
                            Yii::error($e->getMessage(), 'ConsoleLeadController:UpdateByTask:Snooze');
                        }

                        // status_description-  Was deleted
//                        $lead->status_description = 'System AutoChange status to SNOOZE ('.$leadItem['checked_cnt'] .'/'. $leadItem['all_cnt'] . ' tasks completed)';
                    }
                    /*else {
                        $lead->status = Lead::STATUS_FOLLOW_UP;
                        $lead->status_description = 'System AutoChange status to FOLLOW_UP ('.$leadItem['checked_cnt'] .'/'. $leadItem['all_cnt'] . ' tasks completed)';
                    }*/

                    echo ' - Lead: ' . $lead->id . ' - ' . $leadItem['checked_cnt'] . '/' . $leadItem['all_cnt'] . " tasks completed \r\n";
                }
            }
        }




        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    public function actionLeadToTrash(): void
    {
        $now = (new \DateTime());
        $report = [];
        $leads = Lead::find()->alias('lead')->joinWith(['leadFlightSegments as segment'])
            ->andWhere(['lead.status' => Lead::STATUS_FOLLOW_UP])
            ->andWhere(['<', 'segment.departure', $now->format('Y-m-d')])
            ->groupBy('lead.id');

        $leads->orWhere(['AND',
            ['IN', 'lead.status', Lead::TRAVEL_DATE_PASSED_STATUS_LIST],
            [
                '<',
                'segment.departure',
                $now->modify('-' . SettingHelper::getLeadTravelDatesPassedTrashedHours() . ' hours')->format('Y-m-d')
            ],
        ]);

        $count = $leads->count();

        foreach ($leads->batch(100) as $leadsBatch) {
            /** @var Lead $lead */
            foreach ($leadsBatch as $lead) {
                try {
                    $lead->trash(null, null, 'Auto Trash leads with Travel Dates Passed');
                    $this->leadRepository->save($lead);
                    $report[$lead->id] = $item = 'Lead: ' . $lead->id . ' -> Trashed';
                    echo $item . PHP_EOL;
                } catch (\Throwable $exception) {
                    $report[] = $item = 'Lead: ' . $lead->id . ' not updated';
                    echo $item . PHP_EOL;
                    \Yii::error(
                        AppHelper::throwableLog($exception),
                        'Lead:LeadToTrash'
                    );
                }
            }
        }

        echo $count . ' Leads with Travel Dates Passed moved in Trash' . PHP_EOL;
        $message = '0 leads trashed';

        if (count($report) > 0) {
            $message = count($report) . ' leads trashed. [' . implode(', ', array_keys($report)) . ']';
        }

        Yii::info($message, 'info\CronLeadToTrash');
    }

    public function actionUpdateHybridUid(int $limit = 100, int $offset = 0, ?string $dateFrom = null, ?string $dateTo = null)
    {
        $time_start = microtime(true);

        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

        try {
            $format = 'Y-m-d H:i:s';
            if ($dateFrom) {
                $from = \DateTime::createFromFormat($format, $dateFrom);
                if (!$from || !$this->validateDate($from, $dateFrom, $format)) {
                    throw new InvalidArgumentException('DateFrom: ' . $dateFrom . ' invalid format; Expected format: ' . $format);
                }
            }
            if ($dateTo) {
                $to = \DateTime::createFromFormat($format, $dateTo);
                if (!$to || !$this->validateDate($to, $dateTo, $format)) {
                    throw new InvalidArgumentException('DateTo: ' . $dateTo . ' invalid format; Expected format: ' . $format);
                }
            }

            $query = Lead::find()
                ->select(['id', 'bo_flight_id'])
                ->where(new Expression("(hybrid_uid is null or hybrid_uid = '')"))
                ->andWhere(new Expression("(bo_flight_id is not null and bo_flight_id <> 0)"))
                ->andWhere(['status' => Lead::STATUS_SOLD])
                ->orderBy(['id' => SORT_ASC]);
            if ($limit) {
                $query->limit($limit);
            }
            if ($offset) {
                $query->offset($offset);
            }
            if ($dateFrom && $dateTo) {
                $query->andWhere(['between', 'created', $from->format($format), $to->format($format)]);
            }

            $rows = $query->all();
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableLog($e), 'console::LeadController::actionUpdateHybridUid::Throwable');
            echo Console::renderColoredString('%r --- Error %n ' . $e->getMessage()), PHP_EOL;
            die;
        }

        $caseSaleService = Yii::createObject(CasesSaleService::class);

        $errors = [];
        $n = 0;
        $updatedRows = 0;
        $updatedData = [];
        $total = count($rows);
        echo Console::renderColoredString('%y --- Total rows: %n' . $total), PHP_EOL;
        Console::startProgress(0, $total, 'Counting objects: ', false);
        foreach ($rows as $row) {
            try {
                $saleData = $caseSaleService->detailRequestToBackOffice($row->bo_flight_id, 0, 120, 1);
                if (!empty($saleData['bookingId'])) {
                    $resultUpdate = (new Query())->createCommand()->update(Lead::tableName(), [
                        'hybrid_uid' => $saleData['bookingId']
                    ], [
                        'id' => $row->id
                    ])->execute();

                    if (!$resultUpdate) {
                        throw new \RuntimeException('Update failed: ' . $row->getErrorSummary(true)[0] . '; LeadId: ' . $row->id . '; BoFlightId: ' . $row->bo_flight_id . '; ');
                    }

                    $updatedRows++;
                    $updatedData[] = [
                        'lead' => $row->id,
                        'boFlightId' => $row->bo_flight_id,
                        'hybridUid' => $saleData['bookingId']
                    ];
                } else {
                    throw new \RuntimeException('BookingId is empty in result from BO'  . '; LeadId: ' . $row->id . '; BoFlightId: ' . $row->bo_flight_id . '; ');
                }
            } catch (BoResponseException | \RuntimeException $e) {
                $errors[] = $e->getMessage() . '; LeadId: ' . $row->id . '; BoFlightId: ' . $row->bo_flight_id;
            }
            Console::updateProgress($n, $total);
            $n++;
        }
        Console::endProgress("done." . PHP_EOL);

        Yii::info([
            'totalRowsFromSelect' => $total,
            'updatedRows' => $updatedRows,
            'updatedData' => $updatedData,
            'errorsCount' => count($errors),
            'errors' => $errors
        ], 'info\console::LeadController::actionUpdateHybridUid');

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);
        foreach ($errors as $error) {
            echo Console::renderColoredString('%r --- Errors %n ' . $error), PHP_EOL;
        }
        printf(PHP_EOL . 'Execute Time: %s' . PHP_EOL, $this->ansiFormat($time . ' s', Console::FG_RED));
        printf(PHP_EOL . ' --- End [' . date('Y-m-d H:i:s') . '] %s ---' . PHP_EOL . PHP_EOL, $this->ansiFormat(self::class . '\\' . $this->action->id, Console::FG_YELLOW));
    }

    public function actionToExtraQueue(): void
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

        /** @fflag FFlag::FF_LPP_ENABLE, Lead Poor Processing Enable/Disable */
        if (!Yii::$app->ff->can(FFlag::FF_KEY_LPP_ENABLE)) {
            echo Console::renderColoredString('%y --- Feature Flag (' . FFlag::FF_KEY_LPP_ENABLE . ') not enabled %n'), PHP_EOL;
            exit();
        }

        $time_start = microtime(true);
        $currentDT = new \DateTimeImmutable();
        $leadsExpiration = Lead::find()
            ->select(Lead::tableName() . '.id')
            ->addSelect('lpp_lppd_id')
            ->innerJoin(LeadPoorProcessing::tableName(), 'id = lpp_lead_id')
            ->where(['status' => Lead::STATUS_PROCESSING])
            ->andWhere(['<=', 'lpp_expiration_dt', $currentDT->format('Y-m-d H:i:s')])
            ->orderBy(['id' => SORT_ASC])
            ->distinct()
            ->asArray()
            ->all()
        ;

        $count = count($leadsExpiration);
        $processed = 0;
        Console::startProgress($processed, $count);

        foreach ($leadsExpiration as $item) {
            $logData = ['leadId' => $item['id']];
            try {
                (new LeadToExtraQueueService($item['id'], $item['lpp_lppd_id'], $this->leadRepository))->handle();
                $processed++;
                Console::updateProgress($processed, $count);
            } catch (\RuntimeException | \DomainException $throwable) {
                $processed--;
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
                \Yii::warning($message, 'LeadController:actionToExtraQueue:Exception');
            } catch (\Throwable $throwable) {
                $processed--;
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
                \Yii::error($message, 'LeadController:actionToExtraQueue:Throwable');
            }
            Console::updateProgress($processed, $count);
        }

        Console::endProgress(false);

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);
        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time . ' s] %g %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- Processed: %w[' . $processed . '/' . $count . '] %g %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;
    }

    public function actionLppScheduledCommunication(): void
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

        /** @fflag FFlag::FF_LPP_ENABLE, Lead Poor Processing Enable/Disable */
        if (!Yii::$app->ff->can(FFlag::FF_KEY_LPP_ENABLE)) {
            echo Console::renderColoredString('%y --- Feature Flag (' . FFlag::FF_KEY_LPP_ENABLE . ') not enabled %n'), PHP_EOL;
            exit();
        }

        $time_start = microtime(true);
        $scheduledCommunicationRule = LeadPoorProcessingDataQuery::getRuleByKey(
            LeadPoorProcessingDataDictionary::KEY_SCHEDULED_COMMUNICATION,
            true
        );
        if (!$scheduledCommunicationRule) {
            echo Console::renderColoredString('%y --- Rule(' . LeadPoorProcessingDataDictionary::KEY_SCHEDULED_COMMUNICATION .
                ') not enabled %n'), PHP_EOL;
            exit();
        }

        $scheduledCommunicationRuleService = new ScheduledCommunicationService($scheduledCommunicationRule);
        if (!$firstLeadUserData = LeadUserData::find()->orderBy(['lud_created_dt' => SORT_ASC])->one()) {
            echo Console::renderColoredString('%y --- LeadUserData not found %n'), PHP_EOL;
            exit();
        }

        $currentDT = new \DateTimeImmutable();
        $dateRule = $currentDT->modify('-' . $scheduledCommunicationRuleService->getIntervalHour() . ' hours');

        $query = LeadFlow::find()
            ->alias('lead_flow')
            ->select(['lead_flow.lead_id', 'lead_flow.lf_owner_id AS owner_id'])
            ->innerJoin(
                Lead::tableName() . ' AS leads',
                'leads.id = lead_flow.lead_id AND leads.employee_id = lf_owner_id AND leads.status = ' . Lead::STATUS_PROCESSING
            )
            ->where(['lead_flow.status' => Lead::STATUS_PROCESSING])
            ->andWhere(['>', 'leads.created', $firstLeadUserData->lud_created_dt])
            ->andHaving(['<', 'MAX(lead_flow.created)', $dateRule->format('Y-m-d H:i:s')])
            ->orderBy(['leads.id' => SORT_ASC])
            ->groupBy(['lead_flow.lead_id', 'lead_flow.lf_owner_id'])
            ->asArray()
            ->all()
        ;

        $lppLeads = [];
        foreach ($query as $item) {
            $isCallOutCommunicationExist = LeadUserData::find()
                    ->select(new Expression('COUNT(*) AS call_out_cnt'))
                    ->where(['lud_type_id' => LeadUserDataDictionary::TYPE_CALL_OUT])
                    ->andWhere(['>=', 'lud_created_dt', $dateRule->format('Y-m-d H:i:s')])
                    ->andWhere(['lud_lead_id' => $item['lead_id']])
                    ->andWhere(['lud_user_id' => $item['owner_id']])
                    ->andHaving(['>=', 'call_out_cnt', $scheduledCommunicationRuleService->getCallOut()])
                    ->exists()
            ;
            if (!$isCallOutCommunicationExist) {
                $lppLeads[$item['lead_id']] = LeadUserDataDictionary::TYPE_CALL_OUT;
                continue;
            }

            $isSmsOutCommunicationExist = LeadUserData::find()
                    ->select(new Expression('COUNT(*) AS sms_out_cnt'))
                    ->where(['lud_type_id' => LeadUserDataDictionary::TYPE_SMS_OUT])
                    ->andWhere(['>=', 'lud_created_dt', $dateRule->format('Y-m-d H:i:s')])
                    ->andWhere(['lud_lead_id' => $item['lead_id']])
                    ->andWhere(['lud_user_id' => $item['owner_id']])
                    ->andHaving(['>=', 'sms_out_cnt', $scheduledCommunicationRuleService->getSmsOut()])
                    ->exists()
            ;
            if (!$isSmsOutCommunicationExist) {
                $lppLeads[$item['lead_id']] = LeadUserDataDictionary::TYPE_SMS_OUT;
                continue;
            }

            $isEmailOutCommunicationExist = LeadUserData::find()
                    ->select(new Expression('COUNT(*) AS email_offer_cnt'))
                    ->where(['lud_type_id' => LeadUserDataDictionary::TYPE_EMAIL_OFFER])
                    ->andWhere(['>=', 'lud_created_dt', $dateRule->format('Y-m-d H:i:s')])
                    ->andWhere(['lud_lead_id' => $item['lead_id']])
                    ->andWhere(['lud_user_id' => $item['owner_id']])
                    ->andHaving(['>=', 'email_offer_cnt', $scheduledCommunicationRuleService->getEmailOffer()])
                    ->exists()
            ;
            if (!$isEmailOutCommunicationExist) {
                $lppLeads[$item['lead_id']] = LeadUserDataDictionary::TYPE_EMAIL_OFFER;
                continue;
            }
        }

        $count = count($lppLeads);
        $processed = 0;
        Console::startProgress($processed, $count);

        foreach ($lppLeads as $leadId => $firstReasonId) {
            $logData = ['leadId' => $leadId];
            try {
                LeadPoorProcessingService::addLeadPoorProcessingJob(
                    (int) $leadId,
                    [LeadPoorProcessingDataDictionary::KEY_SCHEDULED_COMMUNICATION],
                    $scheduledCommunicationRule->lppd_description
                );

                $processed++;
                Console::updateProgress($processed, $count);
            } catch (\RuntimeException | \DomainException $throwable) {
                $processed--;
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
                \Yii::info($message, 'LeadController:actionLppScheduledCommunication:Exception');
            } catch (\Throwable $throwable) {
                $processed--;
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
                \Yii::error($message, 'LeadController:actionLppScheduledCommunication:Throwable');
            }
            Console::updateProgress($processed, $count);
        }

        Console::endProgress(false);

        \Yii::info(
            [
                'count' => $count,
                'processed' => $processed,
                'LPP' => $lppLeads,
            ],
            'info\LeadController:actionLppScheduledCommunication:result'
        );

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);
        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time . ' s] %g %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- Processed: %w[' . $processed . '/' . $count . '] %g %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;
    }

    private function validateDate(\DateTime $dateObject, string $date, string $format = 'Y-m-d H:i:s'): bool
    {
        return $dateObject->format($format) === $date;
    }
}
