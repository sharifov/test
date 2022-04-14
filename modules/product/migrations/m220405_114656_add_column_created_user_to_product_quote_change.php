<?php

namespace modules\product\migrations;

use yii\db\Migration;

/**
 * Class m220405_114656_add_column_created_user_to_product_quote_change
 */
class m220405_114656_add_column_created_user_to_product_quote_change extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_quote_change}}', 'pqc_created_user_id', $this->integer());
        $this->addForeignKey('FK-product_quote_change-pqc_created_user_id', '{{%product_quote_change}}', 'pqc_created_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-product_quote_change-pqc_created_user_id', '{{%product_quote_change}}');
        $this->dropColumn('{{%product_quote_change}}', 'pqc_created_user_id');
    }
}
