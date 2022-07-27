<?php

use yii\db\Migration;

/**
 * Class m220725_123953_changed_utsl_old_status_column_to_nullable_on_user_task_status_log
 */
class m220725_123953_changed_utsl_old_status_column_to_nullable_on_user_task_status_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%user_task_status_log}}', 'utsl_old_status', $this->smallInteger()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%user_task_status_log}}', 'utsl_old_status', $this->smallInteger()->notNull());
    }
}
