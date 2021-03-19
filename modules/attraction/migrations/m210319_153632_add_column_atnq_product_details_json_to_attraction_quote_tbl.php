<?php

namespace modules\attraction\migrations;

use yii\db\Migration;

/**
 * Class m210319_153632_add_column_atnq_product_details_json_to_attraction_quote_tbl
 */
class m210319_153632_add_column_atnq_product_details_json_to_attraction_quote_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%attraction_quote}}', 'atnq_product_details_json', $this->json());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%attraction_quote}}', 'atnq_product_details_json');
    }
}
