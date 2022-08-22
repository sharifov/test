<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%object_task}}`.
 */
class m220809_145308_create_object_task_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%object_task}}', [
            'ot_uuid' => $this->string(),
            'ot_q_id' => $this->integer(),
            'ot_object' => $this->string()->notNull(),
            'ot_object_id' => $this->integer()->notNull(),
            'ot_execution_dt' => $this->dateTime()->notNull(),
            'ot_command' => $this->string()->notNull(),
            'ot_ots_id' => $this->integer()->notNull(),
            'ot_group_hash' => $this->string()->notNull(),
            'ot_status' => $this->smallInteger()->notNull(),
            'ot_created_dt' => $this->dateTime(),
        ]);

        $this->addPrimaryKey('PK-object_task-ot_uuid', '{{%object_task}}', 'ot_uuid');
        $this->addForeignKey('FK-object_task-ot_q_id', '{{%object_task}}', 'ot_q_id', 'queue', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-object_task-ot_ots_id', '{{%object_task}}', 'ot_ots_id', 'object_task_scenario', 'ots_id', 'CASCADE');
        $this->createIndex('IND-object_task-ot_object', '{{%object_task}}', 'ot_object');
        $this->createIndex('IND-object_task-ot_object_id', '{{%object_task}}', 'ot_object_id');
        $this->createIndex('IND-object_task-ot_group_hash', '{{%object_task}}', 'ot_group_hash');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%object_task}}');
    }
}
