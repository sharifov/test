<?php

use common\models\DepartmentPhoneProject;
use yii\db\Migration;
use yii\helpers\VarDumper;

/**
 * Class m210624_124413_add_params_to_department_phone_project
 */
class m210624_124413_add_params_to_department_phone_project extends Migration
{
    /**
     * {@inheritdoc}
     * @throws JsonException
     */
    public function safeUp()
    {
        $log = [];
        foreach (DepartmentPhoneProject::find()->all() as $departmentPhoneProject) {
            $params = [];
            if ($departmentPhoneProject->dpp_params) {
                $params = json_decode($departmentPhoneProject->dpp_params, true, 512, JSON_THROW_ON_ERROR);
            }
            $params['callFilterGuard'] = [
                'enable' => false,
                'enabledFromDt' => '',
                'enabledToDt' => '',
                'checkService' => ['twilio'],
                'checkAlgorithm' => 'default',
                'trustPercent' => 100,
                'blockList' => [
                    'enabled' => false,
                    'expiredMinutes' => 60
                ],
            ];
            $departmentPhoneProject->dpp_params = json_encode($params);
            if (!$departmentPhoneProject->save()) {
                $log[] = $departmentPhoneProject->getErrors();
            }
        }
        if ($log) {
            VarDumper::dump($log);
        }
    }

    /**
     * {@inheritdoc}
     * @throws JsonException
     */
    public function safeDown()
    {
        $log = [];
        foreach (DepartmentPhoneProject::find()->all() as $departmentPhoneProject) {
            $params = [];
            if ($departmentPhoneProject->dpp_params) {
                $params = json_decode($departmentPhoneProject->dpp_params, true, 512, JSON_THROW_ON_ERROR);
            }
            if (!$params) {
                continue;
            }
            if (empty($params['callFilterGuard'])) {
                continue;
            }
            unset($params['callFilterGuard']);
            $departmentPhoneProject->dpp_params = json_encode($params);
            if (!$departmentPhoneProject->save()) {
                $log[] = $departmentPhoneProject->getErrors();
            }
        }
        if ($log) {
            VarDumper::dump($log);
        }
    }
}
