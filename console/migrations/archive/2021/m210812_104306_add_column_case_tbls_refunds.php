<?php

use yii\db\Migration;

/**
 * Class m210812_104306_add_column_case_tbls_refunds
 */
class m210812_104306_add_column_case_tbls_refunds extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order_refund}}', 'orr_case_id', $this->integer());
        $this->addForeignKey(
            'FK-order_refund-case',
            '{{%order_refund}}',
            ['orr_case_id'],
            '{{%cases}}',
            ['cs_id'],
            'SET NULL',
            'CASCADE',
        );

        $this->addColumn('{{%product_quote_refund}}', 'pqr_case_id', $this->integer());
        $this->addForeignKey(
            'FK-product_quote_refund-case',
            '{{%product_quote_refund}}',
            ['pqr_case_id'],
            '{{%cases}}',
            ['cs_id'],
            'SET NULL',
            'CASCADE',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order_refund}}', 'orr_case_id');
        $this->dropColumn('{{%product_quote_refund}}', 'pqr_case_id');
    }
}
