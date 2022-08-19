<?php

use yii\db\Migration;

/**
 * Class m220818_094439_add_column_ots_enable_to_object_task_scenario_table
 */
class m220818_094439_add_column_ots_enable_to_object_task_scenario_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%object_task_scenario}}',
            'ots_enable',
            $this->boolean()->defaultValue(0)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%object_task_scenario}}', 'ots_enable');
    }
}
