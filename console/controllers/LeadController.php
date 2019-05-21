<?php

namespace console\controllers;

use common\models\Lead;
use common\models\Task;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\VarDumper;
use Yii;

class LeadController extends Controller
{
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
                       $lead->status = Lead::STATUS_FOLLOW_UP;
                       $lead->status_description = 'System AutoChange status to FOLLOW_UP ('.$leadItem['checked_cnt'] .'/'. $leadItem['all_cnt'] . ' tasks completed)';


                       if ($lead->employee_id && !$lead->sendNotification('lead-status-follow-up', $lead->employee_id, null, $lead)) {
                           Yii::warning('Not send Email notification to employee_id: ' . $lead->employee_id . ', lead: ' . $lead->id, 'Console:LeadController:UpdateByTasks:sendNotification');
                       }

                   }

                   $lead->update();

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
                        $lead->status = Lead::STATUS_SNOOZE;
                        $lead->snooze_for = date('Y-m-d', strtotime('+3 days'));
                        $lead->status_description = 'System AutoChange status to SNOOZE ('.$leadItem['checked_cnt'] .'/'. $leadItem['all_cnt'] . ' tasks completed)';
                    }
                    /*else {
                        $lead->status = Lead::STATUS_FOLLOW_UP;
                        $lead->status_description = 'System AutoChange status to FOLLOW_UP ('.$leadItem['checked_cnt'] .'/'. $leadItem['all_cnt'] . ' tasks completed)';
                    }*/

                    $lead->update();

                    echo ' - Lead: '.$lead->id. ' - ' .$leadItem['checked_cnt'] .'/'. $leadItem['all_cnt'] . " tasks completed \r\n";
                }
            }
        }




        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }



}