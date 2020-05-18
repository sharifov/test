<?php

use yii\db\Migration;

/**
 * Class m190304_172256_alter_column_tbl_call
 */
class m190304_172256_alter_column_tbl_call extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%call}}', 'c_account_sid', $this->string(34));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%call}}', 'c_account_sid', $this->string(34)->notNull());
    }
}
