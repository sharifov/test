<?php

use yii\db\Migration;

/**
 * Class m220523_122721_alter_table_user_shift_schedule_log_add_column
 */
class m220523_122721_alter_table_user_shift_schedule_log_add_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_shift_schedule_log}}', 'ussl_action_type', $this->tinyInteger()->after('ussl_uss_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_shift_schedule_log}}', 'ussl_action_type');
    }
}
