<?php

namespace console\controllers;

use common\models\Lead;
use common\models\LeadLog;
use common\models\Note;
use common\models\Quote;
use common\models\Reason;
use yii\console\Controller;
use common\models\LeadTask;
use common\models\Task;
use Yii;
use yii\helpers\Console;
use yii\helpers\VarDumper;

class MonitorFlowController extends Controller
{

    /**
     * @param bool $test
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionWatchDogDeclineQuote($test = false)
    {

        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        $quotes = Quote::find()->select(['quotes.id', 'quotes.uid', 'quotes.lead_id', 'quotes.created'])
            ->joinWith('lead')
            ->where(['quotes.status' => [Quote::STATUS_CREATED, Quote::STATUS_SEND, Quote::STATUS_OPENED]])
            ->andWhere(['<=', 'quotes.created', date('Y-m-d H:i:s', strtotime('-1 day'))])
            ->andWhere(['NOT IN', 'leads.status', [Lead::STATUS_BOOKED, Lead::STATUS_SOLD, Lead::STATUS_PENDING]])
            ->orderBy(['quotes.id' => SORT_DESC])
            //->limit(500)
            ->all();


        //echo $quotes->createCommand()->getRawSql(); exit;

        if ($test) {
            echo sprintf('Count: %s', count($quotes)) . PHP_EOL;
        }
        //exit;
        //$now = time();
        foreach ($quotes as $quote) {

            // VarDumper::dump($quote->attributes); exit;

            $existQuote = Quote::find()->where(['lead_id' => $quote->lead_id, 'status' => Quote::STATUS_APPLIED])->exists();
            if ($existQuote) {
                if ($test) {
                   echo 'Exist alternative applied quote Lead ' .   $quote->lead_id . "\r\n";
                }
                continue;
            }

            //$limitTime = strtotime($quote->created . '+1 day');
            //if ($limitTime <= $now) {
            $quote->status = Quote::STATUS_DECLINED;
            if ($quote->update(false)) {
                if ($test) {
                    echo sprintf('Decline quote: %s. FR: %d, Created: %s', $quote->uid, $quote->lead_id, $quote->created) . PHP_EOL;
                }
            } else {
                Yii::error('Quote: ' . $quote->id .', '. VarDumper::dumpAsString($quote->errors), 'console:actionWatchDogDeclineQuote:Quote:save');
                if($test) {
                    echo 'Quote: ' . $quote->id . ' ' .VarDumper::dumpAsString($quote->errors)."\r\n";
                }
            }
            //}
        }

        if ($test) {
            echo sprintf('Count: %s', count($quotes)) . PHP_EOL;
        }

        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    public function actionOnWake()
    {
        /**
         * @var $objects Lead[]
         */
        $objects = Lead::findAll([
            'status' => Lead::STATUS_SNOOZE
        ]);
        foreach ($objects as $object) {
            $diff = strtotime($object->snooze_for);
            if ($diff <= time()) {
                $object->status = Lead::STATUS_PROCESSING;
                $object->snooze_for = null;
                $object->save();
            }
        }
    }

    public function actionFollowUp($test = false)
    {
        /**
         * @var $objects Lead[]
         */
        $objects = Lead::findAll([
            'status' => [Lead::STATUS_PROCESSING, Lead::STATUS_ON_HOLD]
        ]);

        foreach ($objects as $object) {
            /**
             * @var $lastLog Note
             */
            $lastLog = Note::find()->where(['lead_id' => $object->id])
                ->orderBy('id DESC')->one();
            if ($lastLog == null) {
                $date = $object->updated;
            } else {
                $date = (strtotime($lastLog->created) < strtotime($object->updated))
                    ? $object->updated
                    : $lastLog->created;
            }

            $emails = $object->client->clientEmails;
            $now = date('Y-m-d H:i:s');
            if ($test) {
                echo 'Lead date: ' . $date . '. Now date: ' . date('Y-m-d H:i:s') . PHP_EOL;
            }
            if (!empty($emails)) {
                if ($test) {
                    echo $object->id . ' - status: ' . $object->status . ' Exist email' . PHP_EOL;
                }
                continue;
            } else {
                if ($test) {
                    echo $object->id . ' - status: ' . $object->status . ' NO Exist email' . PHP_EOL;
                }
            }

            $diff = strtotime(sprintf('%s + 48 hours', $date));

            if ($diff <= strtotime($now)) {
                $object->status = $object::STATUS_FOLLOW_UP;
                $object->save(false);
                if ($test) {
                    var_dump($object->getErrors());
                }
                $reason = new Reason();

                if ($test) {
                    echo $object->id . ' - status: ' . $object->status . PHP_EOL;
                }
                $reason->reason = sprintf('No activity for more than 48 hours');
                $reason->employee_id = null;
                $reason->lead_id = $object->id;
                $reason->save();
            }
        }
    }
}