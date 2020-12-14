<?php

use common\models\Department;
use sales\model\department\department\Type;
use yii\db\Migration;
use yii\helpers\VarDumper;

/**
 * Class m201214_082214_add_department_case_params
 */
class m201214_082214_add_department_case_params extends Migration
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
            $params['object']['case']['sendFeedback'] = false;
            $params['object']['case']['feedbackTemplateTypeKey'] = '';
            $params['object']['case']['feedbackEmailFrom'] = '';
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
            if (array_key_exists('sendFeedback', $params['object']['case'])) {
                unset($params['object']['case']['sendFeedback']);
            }
            if (array_key_exists('feedbackTemplateTypeKey', $params['object']['case'])) {
                unset($params['object']['case']['feedbackTemplateTypeKey']);
            }
            if (array_key_exists('feedbackEmailFrom', $params['object']['case'])) {
                unset($params['object']['case']['feedbackEmailFrom']);
            }
            $department->dep_params = json_encode($params);
            if (!$department->save()) {
                VarDumper::dump($department->getErrors());
            }
        }
    }
}
