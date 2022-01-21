<?php

use yii\db\Migration;

/**
 * Class m210209_093349_add_index_for_css_charge_type_column_case_sale_tbl
 */
class m210209_093349_add_index_for_css_charge_type_column_case_sale_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createIndex('idx-case_sale-css_charge_type', 'case_sale', 'css_charge_type');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropIndex('idx-case_sale-css_charge_type', 'case_sale');
    }
}
