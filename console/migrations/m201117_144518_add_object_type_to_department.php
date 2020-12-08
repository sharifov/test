<?php

use common\models\Department;
use sales\model\department\department\Type;
use yii\db\Migration;
use yii\helpers\VarDumper;

/**
 * Class m201117_144518_add_object_type_to_department
 */
class m201117_144518_add_object_type_to_department extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $department = Department::find()->andWhere(['dep_id' => Department::DEPARTMENT_SALES])->one();
        if ($department) {
            $params = [];
            if ($department->dep_params) {
                $params = json_decode($department->dep_params, true);
            }
            $params['object'] = [
                'type' => Type::LEAD,
                'lead' => [
                    'createOnCall' => true,
                    'createOnSms' => false,
                    'createOnEmail' => false,
                ],
                'case' => [
                    'createOnCall' => false,
                    'createOnSms' => false,
                    'createOnEmail' => false,
                    'trashActiveDaysLimit' => 0,
                ],
            ];
            $department->dep_params = json_encode($params);
            if (!$department->save()) {
                VarDumper::dump($department->getErrors());
            }
        }

        $department = Department::find()->andWhere(['dep_id' => Department::DEPARTMENT_EXCHANGE])->one();
        if ($department) {
            $params = [];
            if ($department->dep_params) {
                $params = json_decode($department->dep_params, true);
            }
            $params['object'] = [
                'type' => Type::CASE,
                'lead' => [
                    'createOnCall' => false,
                    'createOnSms' => false,
                    'createOnEmail' => false,
                ],
                'case' => [
                    'createOnCall' => true,
                    'createOnSms' => false,
                    'createOnEmail' => true,
                    'trashActiveDaysLimit' => 14,
                ],
            ];
            $department->dep_params = json_encode($params);
            if (!$department->save()) {
                VarDumper::dump($department->getErrors());
            }
        }

        $department = Department::find()->andWhere(['dep_id' => Department::DEPARTMENT_SUPPORT])->one();
        if ($department) {
            $params = [];
            if ($department->dep_params) {
                $params = json_decode($department->dep_params, true);
            }
            $params['object'] = [
                'type' => Type::CASE,
                'lead' => [
                    'createOnCall' => false,
                    'createOnSms' => false,
                    'createOnEmail' => false,
                ],
                'case' => [
                    'createOnCall' => true,
                    'createOnSms' => false,
                    'createOnEmail' => true,
                    'trashActiveDaysLimit' => 14,
                ],
            ];
            $department->dep_params = json_encode($params);
            if (!$department->save()) {
                VarDumper::dump($department->getErrors());
            }
        }

        $department = Department::find()->andWhere(['dep_id' => Department::DEPARTMENT_SCHEDULE_CHANGE])->one();
        if ($department) {
            $params = [];
            if ($department->dep_params) {
                $params = json_decode($department->dep_params, true);
            }
            $params['object'] = [
                'type' => Type::CASE,
                'lead' => [
                    'createOnCall' => false,
                    'createOnSms' => false,
                    'createOnEmail' => false,
                ],
                'case' => [
                    'createOnCall' => true,
                    'createOnSms' => false,
                    'createOnEmail' => true,
                    'trashActiveDaysLimit' => 14,
                ],
            ];
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
        /** @var Department $department */
        foreach ($departments as $department) {
            $params = [];
            if ($department->dep_params) {
                $params = json_decode($department->dep_params, true);
            }
            if (array_key_exists('object', $params)) {
                unset($params['object']);
            }
            $department->dep_params = json_encode($params);
            if (!$department->save()) {
                VarDumper::dump($department->getErrors());
            }
        }
    }
}
