<?php

use common\models\Department;
use common\models\DepartmentPhoneProject;
use sales\model\department\department\Type;
use yii\db\Migration;
use yii\helpers\VarDumper;

/**
 * Class m210414_111713_add_queue_distribution_params
 */
class m210414_111713_add_queue_distribution_params extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $departments = Department::find()->all();
        foreach ($departments as $department) {
            $params = [];
            if ($department->dep_params) {
                $params = @json_decode($department->dep_params, true);
                if (!is_array($params)) {
                    $params = [];
                }
            }
            $params['queue_distribution'] = [
                'time_start_call_user_access_general' => null,
                'general_line_user_limit' => null,
                'time_repeat_call_user_access' => null,
            ];
            $department->dep_params = json_encode($params);
            if (!$department->save()) {
                VarDumper::dump($department->getErrors());
            }
        }
        //=================
        $log = [];
        /** @var DepartmentPhoneProject[] $phones */
        $phones = DepartmentPhoneProject::find()->all();
        foreach ($phones as $phone) {
            $params = [];
            if ($phone->dpp_params) {
                $params = @json_decode($phone->dpp_params, true);
                if (!is_array($params)) {
                    $params = [];
                }
            }
            $params['queue_distribution'] = [
                'time_start_call_user_access_general' => null,
                'general_line_user_limit' => null,
                'time_repeat_call_user_access' => null,
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
        $departments = Department::find()->all();
        foreach ($departments as $department) {
            $params = [];
            if ($department->dep_params) {
                $params = @json_decode($department->dep_params, true);
                if (!is_array($params)) {
                    $params = [];
                }
            }
            if (array_key_exists('queue_distribution', $params)) {
                unset($params['queue_distribution']);
            } else {
                continue;
            }
            $department->dep_params = json_encode($params);
            if (!$department->save()) {
                VarDumper::dump($department->getErrors());
            }
        }
        //=================
        $log = [];
        /** @var DepartmentPhoneProject[] $phones */
        $phones = DepartmentPhoneProject::find()->all();
        foreach ($phones as $phone) {
            $params = [];
            if ($phone->dpp_params) {
                $params = @json_decode($phone->dpp_params, true);
                if (!is_array($params)) {
                    $params = [];
                }
            }
            if (array_key_exists('queue_distribution', $params)) {
                unset($params['queue_distribution']);
            } else {
                continue;
            }
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
