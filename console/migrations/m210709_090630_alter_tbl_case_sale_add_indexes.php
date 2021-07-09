<?php

use yii\db\Migration;

/**
 * Class m210709_090630_alter_tbl_case_sale_add_indexes
 */
class m210709_090630_alter_tbl_case_sale_add_indexes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('IND-case_sale-css_sale_book_id', '{{%case_sale}}', 'css_sale_book_id');
        $this->createIndex('IND-case_sale-css_sale_pnr', '{{%case_sale}}', 'css_sale_pnr');
        $this->createIndex('IND-case_sale-css_departure_dt', '{{%case_sale}}', 'css_departure_dt');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-case_sale-css_sale_book_id', '{{%case_sale}}');
        $this->dropIndex('IND-case_sale-css_sale_pnr', '{{%case_sale}}');
        $this->dropIndex('IND-case_sale-css_departure_dt', '{{%case_sale}}');
    }
}
