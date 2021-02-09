<?php

use common\models\DepartmentPhoneProject;
use yii\db\Migration;
use yii\helpers\VarDumper;

/**
 * Class m201121_100224_add_queue_long_time_notification_params_to_dep_phones
 */
class m201121_100224_add_queue_long_time_notification_params_to_dep_phones extends Migration
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
            $params['queue_long_time_notification'] = [
                'enable' => false,
                'wait_time' => 5,
                'role_keys' => [],
            ];
            $phone->dpp_params = json_encode($params);
            if (!$phone->save()) {
                $log[] = $phone->getErrors();
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
            if (!$params) {
                continue;
            }
            if (empty($params['queue_long_time_notification'])) {
                continue;
            }
            unset($params['queue_long_time_notification']);
            $phone->dpp_params = json_encode($params);
            if (!$phone->save()) {
                $log[] = $phone->getErrors();
            }
        }
        if ($log) {
            VarDumper::dump($log);
        }
    }
}
