<?php

use yii\db\Migration;

/**
 * Class m210923_125701_alter_tbl_user_params_add_new_column_call_user_level
 */
class m210923_125701_alter_tbl_user_params_add_new_column_call_user_level extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_params}}', 'up_call_user_level', $this->tinyInteger()->defaultValue(0));
        $this->createIndex('IND-user_prams-up_call_user_level', '{{%user_params}}', 'up_call_user_level');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-user_prams-up_call_user_level', '{{%user_params}}');
        $this->dropColumn('{{%user_params}}', 'up_call_user_level');
    }
}
