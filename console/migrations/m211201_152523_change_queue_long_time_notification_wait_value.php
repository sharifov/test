<?php

use common\models\DepartmentPhoneProject;
use yii\db\Migration;
use yii\helpers\VarDumper;

/**
 * Class m211201_152523_change_queue_long_time_notification_wait_value
 */
class m211201_152523_change_queue_long_time_notification_wait_value extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $log = [];
        /** @var DepartmentPhoneProject[] $phones */
        $phones = DepartmentPhoneProject::find()->all();
        foreach ($phones as $phone) {
            $params = [];
            if ($phone->dpp_params) {
                $params = json_decode($phone->dpp_params, true);
            }
            if (array_key_exists('queue_long_time_notification', $params)) {
                $params['queue_long_time_notification']['wait_time'] = (int)$params['queue_long_time_notification']['wait_time'] * 60;
                $phone->dpp_params = json_encode($params);
                if (!$phone->save()) {
                    $log[] = $phone->getErrors();
                }
            }
        }
        if ($log) {
            VarDumper::dump($log);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $log = [];
        /** @var DepartmentPhoneProject[] $phones */
        $phones = DepartmentPhoneProject::find()->all();
        foreach ($phones as $phone) {
            $params = [];
            if ($phone->dpp_params) {
                $params = json_decode($phone->dpp_params, true);
            }
            if (array_key_exists('queue_long_time_notification', $params)) {
                $params['queue_long_time_notification']['wait_time'] = (int)((int)$params['queue_long_time_notification']['wait_time'] / 60);
                $phone->dpp_params = json_encode($params);
                if (!$phone->save()) {
                    $log[] = $phone->getErrors();
                }
            }
        }
        if ($log) {
            VarDumper::dump($log);
        }
    }
}
