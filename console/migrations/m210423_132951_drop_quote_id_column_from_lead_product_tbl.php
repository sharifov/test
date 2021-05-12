<?php

use yii\db\Migration;

/**
 * Class m210423_132951_drop_quote_id_column_from_lead_product_tbl
 */
class m210423_132951_drop_quote_id_column_from_lead_product_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('FK-lead_product-lp_quote_id', '{{%lead_product}}');
        $this->dropColumn('{{%lead_product}}', 'lp_quote_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%lead_product}}', 'lp_quote_id', $this->integer());
        $this->addForeignKey('FK-lead_product-lp_quote_id', '{{%lead_product}}', 'lp_quote_id', '{{%product_quote}}', 'pq_id', 'CASCADE', 'CASCADE');
    }
}
