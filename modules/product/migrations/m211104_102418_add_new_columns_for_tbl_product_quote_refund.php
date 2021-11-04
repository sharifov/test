<?php

namespace modules\product\migrations;

use yii\db\Migration;

/**
 * Class m211104_102418_add_new_columns_for_tbl_product_quote_refund
 */
class m211104_102418_add_new_columns_for_tbl_product_quote_refund extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_quote_refund}}', 'pqr_client_penalty_amount', $this->decimal(8, 2)->after('pqr_client_refund_amount'));
        $this->addColumn('{{%product_quote_refund}}', 'pqr_client_processing_fee_amount', $this->decimal(8, 2)->after('pqr_client_penalty_amount'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product_quote_refund}}', 'pqr_client_penalty_amount');
        $this->dropColumn('{{%product_quote_refund}}', 'pqr_client_processing_fee_amount');
    }
}
