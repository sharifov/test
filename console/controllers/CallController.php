<?php

namespace console\controllers;

use common\models\Call;
use yii\console\Controller;
use Yii;
use yii\helpers\Console;
use yii\helpers\VarDumper;

class CallController extends Controller
{
    /**
     *  Update Call status for old items
     *
     */
    public function actionUpdateStatus()
    {
        echo $this->ansiFormat('starting console script...' . PHP_EOL, Console::FG_GREEN);
        $dt = new \DateTime('now');
        $dt->modify('-1 hour');
        $items = Call::find()
            ->where(['c_call_status' => [Call::CALL_STATUS_QUEUE, Call::CALL_STATUS_RINGING, Call::CALL_STATUS_IN_PROGRESS]])
            ->andWhere(['<=', 'c_created_dt', $dt->format('Y-m-d H:i:s')])
            ->orderBy(['c_id' => SORT_ASC])
            ->all();

        $out = [];
        $errors = [];
        if ($count = count($items) > 0) {
            echo $this->ansiFormat('Find ' . $count . ' items for update' . PHP_EOL, Console::FG_GREEN);
            foreach ($items AS $call) {
                $old_status = $call->c_call_status;
                $call->c_call_status = Call::CALL_STATUS_COMPLETED;
                if ($call->save()) {
                    $out[] = ['c_id' => $call->c_id,
                        'old_status' => $old_status,
                        'new_status' => Call::CALL_STATUS_COMPLETED,
                    ];
                } else {
                    $errors[] = $call->errors;
                }
            }
        } else {
            echo $this->ansiFormat( 'No items to update ' . PHP_EOL, Console::FG_GREEN);
            return 0;
        }

        Yii::info(VarDumper::dumpAsString(['calls' => $out, 'errors' => $errors]), 'info\Console:CallController:actionUpdateStatus');
        echo $this->ansiFormat(PHP_EOL . 'Finish update' . PHP_EOL, Console::FG_GREEN);
        return 0;
    }
}