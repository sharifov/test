<?php

use frontend\helpers\JsonHelper;
use modules\experiment\models\ExperimentTarget;
use yii\db\Migration;
use src\services\departmentPhoneProject\DepartmentPhoneProjectParamsService;
use common\models\DepartmentPhoneProject;

/**
 * Class m220711_084027_add_experiments_field_to_department_phone_project_params
 */
class m220711_084027_add_experiments_field_to_department_phone_project_params extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $departmentPhones = DepartmentPhoneProject::find()->all();
            foreach ($departmentPhones as $departmentPhone) {
                $dpp_params_array = JsonHelper::decode($departmentPhone->dpp_params);
                if (empty($dpp_params_array['experiments'])) {
                    $dpp_params_array['experiments'] = [['ex_code' => 'test.0', 'enabled' => false]];
                    $departmentPhone->dpp_params = JsonHelper::encode($dpp_params_array);
                    $departmentPhone->save();
                }

            }
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'm220711_084027_add_experiments_field_to_department_phone_project_params:safeUp:Throwable');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $departmentPhones = DepartmentPhoneProject::find()->all();
            foreach ($departmentPhones as $departmentPhone) {
                $dpp_params_array = JsonHelper::decode($departmentPhone->dpp_params);
                unset($dpp_params_array['experiments']);
                $departmentPhone->dpp_params = JsonHelper::encode($dpp_params_array);
                $departmentPhone->save();
            }
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'm220711_084027_add_experiments_field_to_department_phone_project_params:safeDown:Throwable');
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220711_084027_add_experiments_field_to_department_phone_project_params cannot be reverted.\n";

        return false;
    }
    */
}
