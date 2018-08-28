<?php

namespace console\controllers;

use common\models\Lead;
use common\models\LeadLog;
use common\models\Note;
use common\models\Quote;
use common\models\Reason;
use yii\console\Controller;
use Yii;

class MonitorFlowController extends Controller
{
    public function actionWatchDogDeclineQuote($test = false)
    {
        /**
         * @var $quotes Quote[]
         */
        $quotes = Quote::find()
            ->where(['status' => [
                Quote::STATUS_CREATED, Quote::STATUS_SEND,
                Quote::STATUS_OPENED
            ]])->all();
        if ($test) {
            echo sprintf('Count: %s', count($quotes)) . PHP_EOL;
        }
        $now = time();
        foreach ($quotes as $quote) {
            if ($quote->lead->getAppliedAlternativeQuotes() !== null) {
                continue;
            }
            $limitTime = strtotime($quote->created . '+1 day');
            if ($limitTime <= $now) {
                $quote->status = Quote::STATUS_DECLINED;
                if ($quote->save()) {
                    if ($test) {
                        echo sprintf('Decline quote: %s. FR: %d, Created: %s', $quote->uid, $quote->lead_id, $quote->created) . PHP_EOL;
                    }
                }
            }
        }
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
                $object->status = Lead::STATUS_ON_HOLD;
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
            if ($lastLog === null) {
                $date = $object->updated;
            } else {
                $date = $lastLog->created;
            }

            $emails = $object->client->clientEmails;
            if ($test) {
                echo 'Lead date: '. $date . '. Now date: '.date('Y-m-d H:i:s') . PHP_EOL;
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

            if ($diff <= time()) {
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