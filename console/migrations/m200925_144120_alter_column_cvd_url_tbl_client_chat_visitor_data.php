<?php

use yii\db\Migration;

/**
 * Class m200925_144120_alter_column_cvd_url_tbl_client_chat_visitor_data
 */
class m200925_144120_alter_column_cvd_url_tbl_client_chat_visitor_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%client_chat_visitor_data}}', 'cvd_url', $this->string(1000));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%client_chat_visitor_data}}', 'cvd_url', $this->string());
    }
}
