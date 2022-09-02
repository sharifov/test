<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%object_task_status_log}}`.
 */
class m220831_094625_create_object_task_status_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%object_task_status_log}}', [
            'otsl_id' => $this->primaryKey(),
            'otsl_ot_uuid' => $this->string()->null(),
            'otsl_old_status' => $this->smallInteger(),
            'otsl_new_status' => $this->smallInteger(),
            'otsl_description' => $this->string(),
            'otsl_created_user_id' => $this->integer()->null(),
            'otsl_created_dt' => $this->dateTime(),
        ]);

        $this->addForeignKey(
            'FK-object_task_status_log-otsl_ot_uuid',
            '{{%object_task_status_log}}',
            'otsl_ot_uuid',
            '{{%object_task}}',
            'ot_uuid',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-object_task_status_log-otsl_created_user_id',
            '{{%object_task_status_log}}',
            'otsl_created_user_id',
            '{{%employees}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%object_task_status_log}}');
    }
}
