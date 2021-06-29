<?php

use yii\db\Migration;

/**
 * Class m210629_061618_add_column_platform_id_tbl_client_chat_message
 */
class m210629_061618_add_column_platform_id_tbl_client_chat_message extends Migration
{
    public function init()
    {
        $this->db = 'db_postgres';
        parent::init(); // TODO: Change the autogenerated stub
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%client_chat_message}}', 'ccm_platform_id', $this->tinyInteger()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%client_chat_message}}', 'ccm_platform_id');
    }
}