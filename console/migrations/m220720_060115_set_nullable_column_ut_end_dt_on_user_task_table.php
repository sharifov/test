<?php

use yii\db\Migration;

/**
 * Class m220720_060115_set_nullable_column_ut_end_dt_on_user_task_table
 */
class m220720_060115_set_nullable_column_ut_end_dt_on_user_task_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%user_task}}', 'ut_end_dt', $this->dateTime()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%user_task}}', 'ut_end_dt', $this->dateTime()->notNull());
    }
}
