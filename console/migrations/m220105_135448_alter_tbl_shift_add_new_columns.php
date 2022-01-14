<?php

use yii\db\Migration;

/**
 * Class m220105_135448_alter_tbl_shift_add_new_columns
 */
class m220105_135448_alter_tbl_shift_add_new_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%shift}}', 'sh_category_id', $this->integer()->after('sh_name'));
        $this->addColumn('{{%shift}}', 'sh_title', $this->string(255)->after('sh_name'));
        $this->addForeignKey('FK-shift-sh_category_id', '{{%shift}}', 'sh_category_id', '{{%shift_category}}', 'sc_id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-shift-sh_category_id', '{{%shift}}');
        $this->dropColumn('{{%shift}}', 'sh_category_id');
        $this->dropColumn('{{%shift}}', 'sh_title');
    }
}
