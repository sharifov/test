<?php

use yii\db\Migration;

/**
 * Class m181031_091717_add_service_fee_column_on_quote_price_table
 */
class m181031_091717_add_service_fee_column_on_quote_price_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%quote_price}}', 'service_fee', 'FLOAT DEFAULT 0 AFTER extra_mark_up');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%quote_price}}', 'service_fee');
    }
}
