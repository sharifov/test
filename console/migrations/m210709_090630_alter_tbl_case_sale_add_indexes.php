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
        $this->createIndex('IND_case_sale_sale_id', '{{%case_sale}}', 'css_sale_book_id');
        $this->createIndex('IND_case_css_sale_pnr', '{{%case_sale}}', 'css_sale_pnr');
        $this->createIndex('IND_case_css_charged', '{{%case_sale}}', 'css_charged');
        $this->createIndex('IND_case_css_profit', '{{%case_sale}}', 'css_profit');
        $this->createIndex('IND_case_css_out_date', '{{%case_sale}}', 'css_out_date');
        $this->createIndex('IND_case_css_in_date', '{{%case_sale}}', 'css_in_date');
        $this->createIndex('IND_case_css_charge_type', '{{%case_sale}}', 'css_charge_type');
        $this->createIndex('IND_case_css_departure_dt', '{{%case_sale}}', 'css_departure_dt');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND_case_sale_sale_id', '{{%case_sale}}');
        $this->dropIndex('IND_case_css_sale_pnr', '{{%case_sale}}');
        $this->dropIndex('IND_case_css_charged', '{{%case_sale}}');
        $this->dropIndex('IND_case_css_profit', '{{%case_sale}}');
        $this->dropIndex('IND_case_css_out_date', '{{%case_sale}}');
        $this->dropIndex('IND_case_css_in_date', '{{%case_sale}}');
        $this->dropIndex('IND_case_css_charge_type', '{{%case_sale}}');
        $this->dropIndex('IND_case_css_departure_dt', '{{%case_sale}}');
    }
}
