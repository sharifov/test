<?php

use yii\db\Migration;

/**
 * Class m220222_055256_add_client_price_columns_to_quote_price
 */
class m220222_055256_add_client_price_columns_to_quote_price extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%quote_price}}', 'qp_client_extra_mark_up', $this->decimal(10, 2));
        $this->addColumn('{{%quote_price}}', 'qp_client_service_fee', $this->decimal(10, 2));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%quote_price}}', 'qp_client_extra_mark_up');
        $this->dropColumn('{{%quote_price}}', 'qp_client_service_fee');
    }
}
