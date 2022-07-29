<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_task_status_log}}`.
 */
class m220722_080150_create_user_task_status_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_task_status_log}}', [
            'utsl_id' => $this->primaryKey(),
            'utsl_ut_id' => $this->integer(),
            'utsl_description' => $this->string(),
            'utsl_old_status' => $this->smallInteger()->notNull(),
            'utsl_new_status' => $this->smallInteger()->notNull(),
            'utsl_created_user_id' => $this->integer(),
            'utsl_created_dt' => $this->dateTime(),
        ]);

        $this->createIndex(
            'IND-user_task_status_log-utsl_ut_id',
            '{{%user_task_status_log}}',
            'utsl_ut_id'
        );

        $this->addForeignKey(
            'FK-user_task_status_log-utsl_created_user_id',
            '{{%user_task_status_log}}',
            'utsl_created_user_id',
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
        $this->dropTable('{{%user_task_status_log}}');
    }
}
