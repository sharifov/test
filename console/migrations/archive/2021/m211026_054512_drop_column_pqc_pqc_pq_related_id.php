<?php

use yii\db\Migration;

/**
 * Class m211026_054512_drop_column_pqc_pqc_pq_related_id
 */
class m211026_054512_drop_column_pqc_pqc_pq_related_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('FK-product_quote_change-product_quote', '{{%product_quote_change}}');

        $this->dropColumn('{{%product_quote_change}}', 'pqc_pqc_pq_related_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%product_quote_change}}', 'pqc_pqc_pq_related_id', $this->integer());

        $this->addForeignKey(
            'FK-product_quote_change-product_quote',
            '{{%product_quote_change}}',
            'pqc_pqc_pq_related_id',
            '{{%product_quote}}',
            'pq_id',
            'SET NULL',
            'CASCADE'
        );
    }
}
