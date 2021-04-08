<?php

use yii\db\Migration;

/**
 * Class m210408_125401_add_columns_billing_id_to_payment_and_invoice
 */
class m210408_125401_add_columns_billing_id_to_payment_and_invoice extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%invoice}}', 'inv_billing_id', $this->integer());
        $this->addForeignKey(
            'FK-invoice-inv_billing_id',
            '{{%invoice}}',
            'inv_billing_id',
            '{{%billing_info}}',
            'bi_id',
            'SET NULL',
            'CASCADE'
        );

        $this->addColumn('{{%payment}}', 'pay_billing_id', $this->integer());
        $this->addForeignKey(
            'FK-payment-pay_billing_id',
            '{{%payment}}',
            'pay_billing_id',
            '{{%billing_info}}',
            'bi_id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-payment-pay_billing_id', '{{%payment}}');
        $this->dropForeignKey('FK-invoice-inv_billing_id', '{{%invoice}}');

        $this->dropColumn('{{%payment}}', 'pay_billing_id');
        $this->dropColumn('{{%invoice}}', 'inv_billing_id');
    }
}
