<?php

use yii\db\Migration;

/**
 * Class m220105_143630_alter_tbl_user_shift_schedule_modify_fk_of_shift_id_column
 */
class m220105_143630_alter_tbl_user_shift_schedule_modify_fk_of_shift_id_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('FK-user_shift_schedule-uss_shift_id', '{{%user_shift_schedule}}');
        $this->alterColumn('{{%user_shift_schedule}}', 'uss_shift_id', $this->integer());
        $this->addForeignKey('FK-user_shift_schedule-uss_shift_id', '{{%user_shift_schedule}}', 'uss_shift_id', '{{%shift}}', 'sh_id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-user_shift_schedule-uss_shift_id', '{{%user_shift_schedule}}');
        $this->alterColumn('{{%user_shift_schedule}}', 'uss_shift_id', $this->integer()->notNull());
        $this->addForeignKey('FK-user_shift_schedule-uss_shift_id', '{{%user_shift_schedule}}', 'uss_shift_id', '{{%shift}}', 'sh_id', 'CASCADE', 'CASCADE');
    }
}
