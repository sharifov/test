<?php

use yii\db\Migration;

/**
 * Class m190521_134122_add_fks_tbl_leads
 */
class m190521_134122_add_fks_tbl_leads extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addForeignKey('FK-leads_project_id', '{{%leads}}', ['project_id'], '{{%projects}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-leads_source_id', '{{%leads}}', ['source_id'], '{{%sources}}', ['id'], 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropForeignKey('FK-leads_project_id', '{{%leads}}');
       $this->dropForeignKey('FK-leads_source_id', '{{%leads}}');
    }

}
