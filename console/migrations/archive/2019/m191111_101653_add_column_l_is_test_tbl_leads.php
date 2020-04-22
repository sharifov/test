<?php

use yii\db\Migration;

/**
 * Class m191111_101653_add_column_l_is_test_tbl_leads
 */
class m191111_101653_add_column_l_is_test_tbl_leads extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn('{{%leads}}', 'l_is_test', $this->tinyInteger(1)->defaultValue(0)->notNull());

		$this->createIndex('IND-leads_l_is_test', '{{%leads}}', 'l_is_test');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->dropIndex('IND-leads_l_is_test', '{{%leads}}');

    	$this->dropColumn('{{%leads}}', 'l_is_test');
    }
}
