<?php

use yii\db\Migration;

/**
 * Class m220105_152944_alter_tbl_user_shift_assign_rename_column
 */
class m220105_152944_alter_tbl_user_shift_assign_rename_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->truncateTable('{{%user_shift_assign}}');
        $this->dropForeignKey('FK-user_shift_assign-usa_ssr_id', '{{%user_shift_assign}}');
        $this->renameColumn('{{%user_shift_assign}}', 'usa_ssr_id', 'usa_sh_id');
        $this->addForeignKey('FK-user_shift_assign-usa_sh_id', '{{%user_shift_assign}}', 'usa_sh_id', '{{%shift}}', 'sh_id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('{{%user_shift_assign}}');
        $this->dropForeignKey('FK-user_shift_assign-usa_sh_id', '{{%user_shift_assign}}');
        $this->renameColumn('{{%user_shift_assign}}', 'usa_sh_id', 'usa_ssr_id');
        $this->addForeignKey('FK-user_shift_assign-usa_ssr_id', '{{%user_shift_assign}}', 'usa_ssr_id', '{{%shift_schedule_rule}}', 'ssr_id', 'CASCADE', 'CASCADE');
    }
}
