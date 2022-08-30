<?php

use yii\db\Migration;

/**
 * Class m220830_090122_add_columns_for_nested_sets_in_tbl_case_category
 */
class m220830_090122_add_columns_for_nested_sets_in_tbl_case_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%case_category}}', 'cc_lft', $this->integer()->notNull());
        $this->addColumn('{{%case_category}}', 'cc_rgt', $this->integer()->notNull());
        $this->addColumn('{{%case_category}}', 'cc_depth', $this->integer()->notNull());
        $this->addColumn('{{%case_category}}', 'cc_tree', $this->integer()->notNull());
        $this->addColumn('{{%case_category}}', 'cc_allow_to_select', $this->tinyInteger(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%case_category}}', 'cc_lft');
        $this->dropColumn('{{%case_category}}', 'cc_rgt');
        $this->dropColumn('{{%case_category}}', 'cc_depth');
        $this->dropColumn('{{%case_category}}', 'cc_tree');
        $this->dropColumn('{{%case_category}}', 'cc_allow_to_select');
    }
}
