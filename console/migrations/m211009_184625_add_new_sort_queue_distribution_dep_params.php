<?php

use yii\db\Migration;

/**
 * Class m211009_184625_add_new_sort_queue_distribution_dep_params
 */
class m211009_184625_add_new_sort_queue_distribution_dep_params extends Migration
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

            $params['queue_distribution']['call_distribution_sort']['priority_level'] = 'DESC';
            $params['queue_distribution']['call_distribution_sort']['gross_profit'] = 'DESC';

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
            $changed = false;
            if (!empty($params['queue_distribution']['call_distribution_sort']['priority_level'])) {
                unset($params['queue_distribution']['call_distribution_sort']['priority_level']);
                $changed = true;
            }
            if (!empty($params['queue_distribution']['call_distribution_sort']['gross_profit'])) {
                unset($params['queue_distribution']['call_distribution_sort']['gross_profit']);
                $changed = true;
            }
            if ($changed) {
                $queryUpdate = new \yii\db\Query();
                $queryUpdate->createCommand()->setSql("update department set dep_params = :param where dep_id = :depId")->bindValues([
                    'param' => @json_encode($params),
                    'depId' => $department['dep_id']
                ])->execute();
            }
        }
    }
}
