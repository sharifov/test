<?php

namespace console\controllers;

use common\models\Call;
use common\models\Lead;
use common\models\LeadFlow;
use common\models\LeadQcall;
use common\models\Task;
use sales\repositories\lead\LeadRepository;
use yii\console\Controller;
use yii\db\Query;
use yii\helpers\Console;
use yii\helpers\VarDumper;
use Yii;

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
            $message = count($report) .' leads returned to call ready. [' . implode(', ', array_keys($report)) . ']';
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
            $message = count($report) .' leads returned to call bugged. [' . implode(', ', array_keys($report)) . ']';
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
            ->where(['offset_gmt' => null])
            ->andWhere(['status' => [Lead::STATUS_PENDING, Lead::STATUS_PROCESSING]])
            ->andWhere(['IS NOT', 'request_ip', null])
            ->orderBy(['id' => SORT_DESC])
            ->limit(20)->all();

            //print_r($leads->createCommand()->getRawSql());

        if($leads) {
            foreach ($leads as $lead) {

                $out = $lead->updateIpInfo();

                if(isset($out['error']) && $out['error']) {
                    echo $lead->id."\r\n";
                    VarDumper::dump($out);
                    echo "\r\n";

                } else {
                    echo $lead->id.' OK - ';
                    if(isset($out['data']['timeZone'])) {
                        VarDumper::dump($out['data']['timeZone']);
                    }
                    echo "\r\n";
                }

                sleep(1);
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
       if($leadsData) {
           foreach ($leadsData as $leadItem) {
               $lead = Lead::findOne($leadItem['lt_lead_id']);
               if($lead) {
                   /*if(!$leadItem['l_answered']) {
                       $lead->status = Lead::STATUS_SNOOZE;
                       $lead->status_description = 'System Autochange status to SNOOZE ('.$leadItem['checked_cnt'] .'/'. $leadItem['all_cnt'] . ' tasks completed)';
                   } else*/

                   if(!$leadItem['l_answered']) {
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

                   echo ' - Lead: '.$lead->id. ' - ' .$leadItem['checked_cnt'] .'/'. $leadItem['all_cnt'] . " tasks completed \r\n";
               }
           }
       }


        $leadsData = Lead::getEndTaskLeads(Task::CAT_ANSWERED_PROCESS);
        echo " ---- TASK CATEGORY 2 ----\n";

        if($leadsData) {
            foreach ($leadsData as $leadItem) {
                $lead = Lead::findOne($leadItem['lt_lead_id']);
                if($lead) {
                    if($leadItem['l_answered']) {
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

                    echo ' - Lead: '.$lead->id. ' - ' .$leadItem['checked_cnt'] .'/'. $leadItem['all_cnt'] . " tasks completed \r\n";
                }
            }
        }




        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }



}