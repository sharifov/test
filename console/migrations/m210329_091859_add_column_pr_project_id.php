<?php

use yii\db\Migration;

/**
 * Class m210329_091859_add_column_pr_project_id
 */
class m210329_091859_add_column_pr_project_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'pr_project_id', $this->integer());

        $this->addForeignKey(
            'FK-product-projects',
            '{{%product}}',
            'pr_project_id',
            'projects',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-product-projects', '{{%product}}');
        $this->dropColumn('{{%product}}', 'pr_project_id');
    }
}
