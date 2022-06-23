<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%object_segment_task}}`.
 */
class m220621_035435_create_object_segment_task_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%object_segment_task}}', [
            'ostl_osl_id' => $this->integer(),
            'ostl_tl_id' => $this->integer(),
            'ostl_created_dt' => $this->dateTime(),
            'ostl_created_user_id' => $this->integer(),
        ]);

        $this->addPrimaryKey('PK-object_segment_task', '{{%object_segment_task}}', ['ostl_osl_id', 'ostl_tl_id']);

        $this->addForeignKey('FK-object_segment_task-ostl_osl_id', '{{%object_segment_task}}', 'ostl_osl_id', '{{%object_segment_list}}', 'osl_id', 'CASCADE');
        $this->addForeignKey('FK-object_segment_task-ostl_tl_id', '{{%object_segment_task}}', 'ostl_tl_id', '{{%task_list}}', 'tl_id', 'CASCADE');
        $this->addForeignKey('FK-object_segment_task-ostl_created_user_id', '{{%object_segment_task}}', 'ostl_created_user_id', '{{%employees}}', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%object_segment_task}}');
    }
}
