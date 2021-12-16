<?php

namespace modules\product\migrations;

use yii\db\Migration;

/**
 * Class m211101_070247_alter_tbl_product_quote_option_refund_add_column
 */
class m211101_070247_alter_tbl_product_quote_option_refund_add_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_quote_option_refund}}', 'pqor_order_refund_id', $this->integer()->after('pqor_id'));
        $this->addForeignKey('FK-product_quote_option_refund-pqor_order_refund_id', '{{%product_quote_option_refund}}', 'pqor_order_refund_id', 'order_refund', 'orr_id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-product_quote_option_refund-pqor_order_refund_id', '{{%product_quote_option_refund}}');
        $this->dropColumn('{{%product_quote_option_refund}}', 'pqor_order_refund_id');
    }
}
