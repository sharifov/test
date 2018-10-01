<?php

namespace console\controllers;

use common\models\Lead;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\Url;

class LeadController extends Controller
{
    public function actionUpdateIpInfo()
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        $leads = Lead::find()
            ->where(['offset_gmt' => null])
            ->andWhere(['status' => [Lead::STATUS_PENDING, Lead::STATUS_PROCESSING]])
            ->andWhere(['IS NOT', 'request_ip', null])
            ->orderBy(['id' => SORT_DESC])
            ->limit(3)->all();

            //print_r($leads->createCommand()->getRawSql());

        if($leads) {
            foreach ($leads as $lead) {
                if($lead->updateIpInfo()) {
                    echo $lead->id." OK\r\n";
                } else {
                    echo $lead->id." Error\r\n";
                }
            }

        }
        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }



    public function actionEmail()
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        $swiftMailer = \Yii::$app->mailer2;
        $body = 'Hi! Меня зовут .... ! Как дела? '.date('Y-m-d H:i:s');
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
    }

}