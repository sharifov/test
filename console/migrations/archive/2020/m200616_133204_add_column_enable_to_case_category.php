<?php

use yii\db\Migration;

/**
 * Class m200616_133204_add_column_enable_to_case_category
 */
class m200616_133204_add_column_enable_to_case_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%case_category}}', 'cc_enabled', $this->boolean()->defaultValue(true));
        $this->createIndex('IND-case_category-cc_enabled', '{{%case_category}}', 'cc_enabled');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-case_category-cc_enabled', '{{%case_category}}');
        $this->dropColumn('{{%case_category}}', 'cc_enabled');
    }
}
