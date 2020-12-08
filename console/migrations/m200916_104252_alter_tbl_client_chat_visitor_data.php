<?php

use yii\db\Migration;

/**
 * Class m200916_104252_alter_tbl_client_chat_visitor_data
 */
class m200916_104252_alter_tbl_client_chat_visitor_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%client_chat_visitor_data}}', 'cvd_referrer', $this->string(1000));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%client_chat_visitor_data}}', 'cvd_referrer', $this->string());
    }
}
