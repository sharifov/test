<?php

use yii\db\Migration;

/**
 * Class m181106_095018_alter_fk_on_delete_quote
 */
class m181106_095018_alter_fk_on_delete_quote extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk-quote_status_log-quote', 'quote_status_log');
        $this->dropForeignKey('fk-quote_price-quotes', 'quote_price');
        $this->addForeignKey('fk-quote_status_log-quote', 'quote_status_log', 'quote_id', 'quotes', 'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-quote_price-quotes', 'quote_price', 'quote_id', 'quotes', 'id','CASCADE','CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-quote_status_log-quote', 'quote_status_log');
        $this->dropForeignKey('fk-quote_price-quotes', 'quote_price');
        $this->addForeignKey('fk-quote_status_log-quote', 'quote_status_log', 'quote_id', 'quotes', 'id');
        $this->addForeignKey('fk-quote_price-quotes', 'quote_price', 'quote_id', 'quotes', 'id');
    }
}
