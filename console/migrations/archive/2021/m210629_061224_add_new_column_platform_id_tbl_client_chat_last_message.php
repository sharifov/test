<?php

use yii\db\Migration;

/**
 * Class m210629_061224_add_new_column_platform_id
 */
class m210629_061224_add_new_column_platform_id_tbl_client_chat_last_message extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%client_chat_last_message}}', 'cclm_platform_id', $this->tinyInteger()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%client_chat_last_message}}', 'cclm_platform_id');
    }
}
