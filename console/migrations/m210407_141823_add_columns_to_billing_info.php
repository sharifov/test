<?php

use yii\db\Migration;

/**
 * Class m210407_141823_add_columns_to_billing_info
 */
class m210407_141823_add_columns_to_billing_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%billing_info}}', 'bi_payment_id', $this->integer());
        $this->addColumn('{{%billing_info}}', 'bi_invoice_id', $this->integer());

        $this->addForeignKey(
            'FK-billing_info-bi_payment_id',
            '{{%billing_info}}',
            'bi_payment_id',
            '{{%payment}}',
            'pay_id',
            'SET NULL',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-billing_info-bi_invoice_id',
            '{{%billing_info}}',
            'bi_invoice_id',
            '{{%invoice}}',
            'inv_id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-billing_info-bi_payment_id', '{{%billing_info}}');
        $this->dropForeignKey('FK-billing_info-bi_invoice_id', '{{%billing_info}}');

        $this->dropColumn('{{%billing_info}}', 'bi_payment_id');
        $this->dropColumn('{{%billing_info}}', 'bi_invoice_id');
    }
}
