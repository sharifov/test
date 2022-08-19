<?php

use yii\db\Migration;

/**
 * Class m220819_032935_add_columns_to_object_task_scenario
 */
class m220819_032935_add_columns_to_object_task_scenario extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%object_task_scenario}}',
            'ots_created_user_id',
            $this->integer()->after('ots_data_json')
        );

        $this->addColumn(
            '{{%object_task_scenario}}',
            'ots_created_dt',
            $this->dateTime()->after('ots_created_user_id')
        );

        $this->addForeignKey(
            'FK-object_task_scenario-ots_created_user_id',
            '{{%object_task_scenario}}',
            'ots_created_user_id',
            'employees',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'FK-object_task_scenario-ots_updated_user_id',
            '{{%object_task_scenario}}',
            'ots_updated_user_id',
            'employees',
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-object_task_scenario-ots_updated_user_id', '{{%object_task_scenario}}');
        $this->dropForeignKey('FK-object_task_scenario-ots_created_user_id', '{{%object_task_scenario}}');
        $this->dropColumn('{{%object_task_scenario}}', 'ots_created_dt');
        $this->dropColumn('{{%object_task_scenario}}', 'ots_created_user_id');
    }
}
