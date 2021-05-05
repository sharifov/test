<?php

use yii\db\Migration;

/**
 * Class m210408_124310_drop_columns_from_billing_info_tbl
 */
class m210408_124310_drop_columns_from_billing_info_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('FK-billing_info-bi_payment_id', '{{%billing_info}}');
        $this->dropForeignKey('FK-billing_info-bi_invoice_id', '{{%billing_info}}');

        $this->dropColumn('{{%billing_info}}', 'bi_payment_id');
        $this->dropColumn('{{%billing_info}}', 'bi_invoice_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
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
}
