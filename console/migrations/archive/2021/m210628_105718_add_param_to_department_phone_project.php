<?php

use common\models\DepartmentPhoneProject;
use frontend\helpers\JsonHelper;
use yii\db\Migration;
use yii\helpers\ArrayHelper;

/**
 * Class m210628_105718_add_param_to_department_phone_project
 */
class m210628_105718_add_param_to_department_phone_project extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $log = [];
        foreach (DepartmentPhoneProject::find()->all() as $departmentPhoneProject) {
            $params = [];
            if ($departmentPhoneProject->dpp_params) {
                $params = JsonHelper::decode($departmentPhoneProject->dpp_params);
            }
            ArrayHelper::setValue($params, 'callFilterGuard.callTerminate', false);

            $departmentPhoneProject->dpp_params = JsonHelper::encode($params);
            if (!$departmentPhoneProject->save()) {
                $log[] = $departmentPhoneProject->getErrors();
            }
        }
        if ($log) {
            \Yii::info(
                $log,
                'info\m210628_105718_add_param_to_department_phone_project:safeUp'
            );
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
                $params = JsonHelper::decode($departmentPhoneProject->dpp_params);
            }
            if (!$params) {
                continue;
            }
            if (empty($params['callFilterGuard'])) {
                continue;
            }

            unset($params['callFilterGuard']['callTerminate']);

            $departmentPhoneProject->dpp_params = json_encode($params);
            if (!$departmentPhoneProject->save()) {
                $log[] = $departmentPhoneProject->getErrors();
            }
        }
        if ($log) {
            \Yii::info(
                $log,
                'info\m210628_105718_add_param_to_department_phone_project:safeDown'
            );
        }
    }
}
