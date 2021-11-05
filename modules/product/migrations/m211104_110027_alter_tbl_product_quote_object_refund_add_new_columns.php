<?php

namespace modules\product\migrations;

use yii\db\Migration;

/**
 * Class m211104_110027_alter_tbl_product_quote_object_refund_add_new_columns
 */
class m211104_110027_alter_tbl_product_quote_object_refund_add_new_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_quote_object_refund}}', 'pqor_client_penalty_amount', $this->decimal(8, 2)->after('pqor_client_refund_amount'));
        $this->addColumn('{{%product_quote_object_refund}}', 'pqor_client_processing_fee_amount', $this->decimal(8, 2)->after('pqor_client_penalty_amount'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product_quote_object_refund}}', 'pqor_client_penalty_amount');
        $this->dropColumn('{{%product_quote_object_refund}}', 'pqor_client_processing_fee_amount');
    }
}
