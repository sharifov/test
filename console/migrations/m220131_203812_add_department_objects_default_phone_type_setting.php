<?php

use common\models\Department;
use src\model\department\department\CallDefaultPhoneType;
use src\model\department\department\EmailDefaultType;
use src\model\department\department\SmsDefaultPhoneType;
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
            if (!empty($params['default_phone_type']) && ($params['default_phone_type'] === 'Only general' || $params['default_phone_type'] === 'General first')) {
                $params['object']['lead']['callDefaultPhoneType'] = CallDefaultPhoneType::GENERAL;
                $params['object']['lead']['smsDefaultPhoneType'] = SmsDefaultPhoneType::GENERAL;
                $params['object']['lead']['emailDefaultType'] = EmailDefaultType::GENERAL;
                $params['object']['case']['callDefaultPhoneType'] = CallDefaultPhoneType::GENERAL;
                $params['object']['case']['smsDefaultPhoneType'] = SmsDefaultPhoneType::GENERAL;
                $params['object']['case']['emailDefaultType'] = EmailDefaultType::GENERAL;
            } else {
                $params['object']['lead']['callDefaultPhoneType'] = CallDefaultPhoneType::PERSONAL;
                $params['object']['lead']['smsDefaultPhoneType'] = SmsDefaultPhoneType::PERSONAL;
                $params['object']['lead']['emailDefaultType'] = EmailDefaultType::PERSONAL;
                $params['object']['case']['callDefaultPhoneType'] = CallDefaultPhoneType::PERSONAL;
                $params['object']['case']['smsDefaultPhoneType'] = SmsDefaultPhoneType::PERSONAL;
                $params['object']['case']['emailDefaultType'] = EmailDefaultType::PERSONAL;
            }

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
            unset(
                $params['object']['lead']['callDefaultPhoneType'],
                $params['object']['lead']['smsDefaultPhoneType'],
                $params['object']['lead']['emailDefaultType'],
                $params['object']['case']['callDefaultPhoneType'],
                $params['object']['case']['smsDefaultPhoneType'],
                $params['object']['case']['emailDefaultType']
            );
            $department->dep_params = Json::encode($params);
            $department->save();
        }
    }
}
