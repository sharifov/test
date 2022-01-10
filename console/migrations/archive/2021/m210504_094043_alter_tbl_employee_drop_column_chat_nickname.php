<?php

use yii\db\Migration;

/**
 * Class m210504_094043_alter_tbl_employee_drop_column_chat_nickname
 */
class m210504_094043_alter_tbl_employee_drop_column_chat_nickname extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%employees}}', 'nickname_client_chat');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%employees}}', 'nickname_client_chat', $this->string(255));
    }
}
