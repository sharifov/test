<?php

namespace modules\product\migrations;

use yii\db\Migration;

/**
 * Class m211108_072110_add_new_column_refund_cost_in_to_tbl_product_quote_refund
 */
class m211108_072110_add_new_column_refund_cost_in_to_tbl_product_quote_refund extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_quote_refund}}', 'pqr_refund_cost', $this->decimal(8, 2)->after('pqr_refund_amount'));
        $this->addColumn('{{%product_quote_refund}}', 'pqr_client_refund_cost', $this->decimal(8, 2)->after('pqr_client_processing_fee_amount'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product_quote_refund}}', 'pqr_refund_cost');
        $this->dropColumn('{{%product_quote_refund}}', 'pqr_client_refund_cost');
    }
}
