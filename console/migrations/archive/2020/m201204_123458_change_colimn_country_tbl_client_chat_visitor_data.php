<?php

use yii\db\Migration;

/**
 * Class m201204_123458_change_colimn_country_tbl_client_chat_visitor_data
 */
class m201204_123458_change_colimn_country_tbl_client_chat_visitor_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%client_chat_visitor_data}}', 'cvd_country', $this->string(100));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%client_chat_visitor_data}}', 'cvd_country', $this->string(50));
    }
}
