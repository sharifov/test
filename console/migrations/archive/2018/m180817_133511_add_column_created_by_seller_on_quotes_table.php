<?php

use yii\db\Migration;

/**
 * Class m180817_133511_add_column_created_by_seller_on_quotes_table
 */
class m180817_133511_add_column_created_by_seller_on_quotes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%quotes}}', 'created_by_seller', $this->boolean()->defaultValue(true));
        $this->addColumn('{{%quotes}}', 'employee_name', $this->string());

        $this->addColumn('{{%quote_price}}', 'uid', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%quotes}}', 'created_by_seller');
        $this->dropColumn('{{%quotes}}', 'employee_name');

        $this->dropColumn('{{%quote_price}}', 'uid');
    }
}
