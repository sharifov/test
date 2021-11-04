<?php

namespace modules\order\migrations;

use yii\db\Migration;

/**
 * Class m211104_111320_alter_tbl_order_refund_add_new_columns
 */
class m211104_111320_alter_tbl_order_refund_add_new_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order_refund}}', 'orr_client_penalty_amount', $this->decimal(8, 2)->after('orr_client_selling_price'));
        $this->addColumn('{{%order_refund}}', 'orr_client_processing_fee_amount', $this->decimal(8, 2)->after('orr_client_penalty_amount'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order_refund}}', 'orr_client_penalty_amount');
        $this->dropColumn('{{%order_refund}}', 'orr_client_processing_fee_amount');
    }
}
