<?php

use yii\db\Migration;

/**
 * Class m220523_090935_add_index_for_user_shift_schedule_log_table
 */
class m220523_090935_add_index_for_user_shift_schedule_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('IND-user_shift_schedule_log-year-month', '{{%user_shift_schedule_log}}', [
            'ussl_year_start',
            'ussl_month_start'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-user_shift_schedule_log-year-month', '{{%user_shift_schedule_log}}');
    }
}
