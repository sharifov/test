<?php

namespace modules\product\migrations;

use yii\db\Migration;

/**
 * Class m210309_071811_alter_tbl_product_quote_option_add_columns
 */
class m210309_071811_alter_tbl_product_quote_option_add_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_quote_option}}', 'pqo_request_data', $this->json());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product_quote_option}}', 'pqo_request_data');
    }
}
