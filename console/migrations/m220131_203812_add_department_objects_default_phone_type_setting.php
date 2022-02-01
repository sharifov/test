<?php

use common\models\Department;
use src\model\department\department\CallDefaultPhoneType;
use yii\db\Migration;
use yii\helpers\Json;

/**
 * Class m220131_203812_add_department_objects_default_phone_type_setting
 */
class m220131_203812_add_department_objects_default_phone_type_setting extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $departments = Department::find()->all();
        foreach ($departments as $department) {
            $params = Json::decode($department->dep_params);
            $params['object']['lead']['callDefaultPhoneType'] = CallDefaultPhoneType::PERSONAL;
            $params['object']['case']['callDefaultPhoneType'] = CallDefaultPhoneType::PERSONAL;
            $department->dep_params = Json::encode($params);
            $department->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $departments = Department::find()->all();
        foreach ($departments as $department) {
            $params = Json::decode($department->dep_params);
            unset($params['object']['lead']['callDefaultPhoneType'], $params['object']['case']['callDefaultPhoneType']);
            $department->dep_params = Json::encode($params);
            $department->save();
        }
    }
}
