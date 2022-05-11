<?php

use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use yii\db\Migration;

/**
 * Class m211025_100545_add_column_pqc_gid
 */
class m211025_100545_add_column_pqc_gid extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_quote_change}}', 'pqc_gid', $this->string(32));

        $productQuoteChanges = ProductQuoteChange::find()->select(['pqc_id'])->asArray()->all();

        foreach ($productQuoteChanges as $pqcId) {
            ProductQuoteChange::updateAll(['pqc_gid' => ProductQuoteChange::generateGid()], ['pqc_id' => $pqcId]);
        }

        $this->alterColumn('{{%product_quote_change}}', 'pqc_gid', $this->string(32)->notNull()->unique());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product_quote_change}}', 'pqc_gid');
    }
}
