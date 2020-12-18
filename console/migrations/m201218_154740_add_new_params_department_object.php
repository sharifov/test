<?php

use common\models\Department;
use yii\db\Migration;
use yii\helpers\VarDumper;

/**
 * Class m201218_154740_add_new_params_department_object
 */
class m201218_154740_add_new_params_department_object extends Migration
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
                $params = json_decode($department->dep_params, true);
            }
            $params['object']['case']['feedbackBookingIdRequired'] = false;
            $department->dep_params = json_encode($params);
            if (!$department->save()) {
                VarDumper::dump($department->getErrors());
            }
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
                $params = json_decode($department->dep_params, true);
            }
            if (array_key_exists('feedbackBookingIdRequired', $params['object']['case'])) {
                unset($params['object']['case']['feedbackBookingIdRequired']);
            }
            $department->dep_params = json_encode($params);
            if (!$department->save()) {
                VarDumper::dump($department->getErrors());
            }
        }
    }
}
