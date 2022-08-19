<?php

use yii\db\Migration;

/**
 * Class m220818_111642_add_column_ots_condition_to_object_task_scenario_table
 */
class m220818_111642_add_column_ots_condition_to_object_task_scenario_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%object_task_scenario}}',
            'ots_condition',
            $this->string(3000)
        );

        $this->addColumn(
            '{{%object_task_scenario}}',
            'ots_condition_json',
            $this->json()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%object_task_scenario}}', 'ots_condition_json');
        $this->dropColumn('{{%object_task_scenario}}', 'ots_condition');
    }
}
