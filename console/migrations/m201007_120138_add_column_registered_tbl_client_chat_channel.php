<?php

use yii\db\Migration;

/**
 * Class m201007_120138_add_column_registered_tbl_client_chat_channel
 */
class m201007_120138_add_column_registered_tbl_client_chat_channel extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%client_chat_channel}}', 'ccc_registered', $this->boolean());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%client_chat_channel}}', 'ccc_registered');
    }
}
