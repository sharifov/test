<?php

use yii\db\Migration;

/**
 * Class m210506_074509_add_column_in_tbl_user_client_chat_data
 */
class m210506_074509_add_column_in_tbl_user_client_chat_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_client_chat_data}}', 'uccd_chat_status_id', $this->tinyInteger(1)->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_client_chat_data}}', 'uccd_chat_status_id');
    }
}
