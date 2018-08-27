<?php

namespace console\controllers;

use common\models\Lead;
use common\models\LeadLog;
use common\models\Reason;
use yii\console\Controller;
use Yii;

class MonitorFlowController extends Controller
{
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
             * @var $lastLog LeadLog
             */
            $lastLog = $object->lastLog();
            if ($lastLog === null) {
                $date = $object->updated;
            } else {
                $date = $lastLog->created;
            }

            $emails = $object->client->clientEmails;
            if (!empty($emails)) {
                if ($test) {
                    echo $object->id . ' - status: ' . $object->status . ' Exist email' . PHP_EOL;
                }
                continue;
            }

            $diff = strtotime(sprintf('%s + 48 hours', $date));

            if ($diff <= time()) {
                $object->status = $object::STATUS_FOLLOW_UP;
                $object->employee_id = null;
                $object->save();
                $reason = new Reason();

                if ($test) {
                    echo $object->flight_request_id . ' - status: ' . $object->status . PHP_EOL;
                }
                $reason->reason = sprintf('No activity for more than 48 hours');
                $reason->employee_id = null;
                $reason->lead_id = $object->id;
                $reason->save();
            }
        }
    }
}