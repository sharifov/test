<?php

namespace modules\product\migrations;

use yii\db\Migration;

/**
 * Class m210305_135254_add_columns_tbl_product_quote
 */
class m210305_135254_add_columns_tbl_product_quote extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_quote}}', 'pq_app_markup', $this->decimal(10, 2));
        $this->addColumn('{{%product_quote}}', 'pq_agent_markup', $this->decimal(10, 2));
        $this->addColumn('{{%product_quote}}', 'pq_service_fee_percent', $this->decimal(5, 2));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product_quote}}', 'pq_app_markup');
        $this->dropColumn('{{%product_quote}}', 'pq_agent_markup');
        $this->dropColumn('{{%product_quote}}', 'pq_service_fee_percent');
    }
}
