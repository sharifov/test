<?php

use yii\db\Migration;

/**
 * Class m210707_122207_alter_tbl_departments_add_new_settings_to_param_field
 */
class m210707_122207_alter_tbl_departments_add_new_settings_to_param_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $query = new \yii\db\Query();
        $query->from('department');

        $departments = $query->all();

        foreach ($departments as $department) {
            $params = @json_decode($department['dep_params'], true);

            $params['queue_distribution']['call_distribution_sort']['general_line_call_count'] = 'ASC';
            $params['queue_distribution']['call_distribution_sort']['phone_ready_time'] = 'ASC';

            $queryUpdate = new \yii\db\Query();
            $queryUpdate->createCommand()->setSql("update department set dep_params = :param where dep_id = :depId")->bindValues([
                'param' => @json_encode($params),
                'depId' => $department['dep_id']
            ])->execute();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $query = new \yii\db\Query();
        $query->from('department');

        $departments = $query->all();

        foreach ($departments as $department) {
            $params = @json_decode($department['dep_params'], true);

            if (!empty($params['queue_distribution']['call_distribution_sort'])) {
                unset($params['queue_distribution']['call_distribution_sort']);
                $queryUpdate = new \yii\db\Query();
                $queryUpdate->createCommand()->setSql("update department set dep_params = :param where dep_id = :depId")->bindValues([
                    'param' => @json_encode($params),
                    'depId' => $department['dep_id']
                ])->execute();
            }
        }
    }
}
