<?php

namespace modules\product\migrations;

use yii\db\Migration;

/**
 * Class m210826_100005_alter_tbl_product_quote_change_add_new_column
 */
class m210826_100005_alter_tbl_product_quote_change_add_new_column extends Migration
{
    private string $tableName = '{{%product_quote_change}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'pqc_is_automate', $this->boolean());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'pqc_is_automate');
    }
}
