<?php

namespace modules\product\migrations;

use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use yii\db\Migration;

/**
 * Class m211008_111744_add_column_pqc_type_id
 */
class m211008_111744_add_column_pqc_type_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_quote_change}}', 'pqc_type_id', $this->tinyInteger());
        ProductQuoteChange::updateAll(['pqc_type_id' => ProductQuoteChange::TYPE_RE_PROTECTION]);
        $this->createIndex('IND-product_quote_change-pqc_type_id', '{{%product_quote_change}}', 'pqc_type_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-product_quote_change-pqc_type_id', '{{%product_quote_change}}');
        $this->dropColumn('{{%product_quote_change}}', 'pqc_type_id');
    }
}
