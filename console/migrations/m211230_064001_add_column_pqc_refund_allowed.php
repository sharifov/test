<?php

use yii\db\Migration;

/**
 * Class m211230_064001_add_column_pqc_refund_allowed
 */
class m211230_064001_add_column_pqc_refund_allowed extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_quote_change}}', 'pqc_refund_allowed', $this->boolean()->defaultValue(true));
        $this->createIndex('IND-product_quote_change-pqc_refund_allowed', '{{%product_quote_change}}', ['pqc_refund_allowed']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-product_quote_change-pqc_refund_allowed', '{{%product_quote_change}}');
        $this->dropColumn('{{%product_quote_change}}', 'pqc_refund_allowed');
    }
}
