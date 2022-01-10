<?php

use yii\db\Migration;

/**
 * Class m210524_102807_add_column_lr_project_id
 */
class m210524_102807_add_column_lr_project_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%lead_request}}', 'lr_project_id', $this->integer());
        $this->addForeignKey(
            'FK-lead_request-project_id',
            '{{%lead_request}}',
            ['lr_project_id'],
            '{{%projects}}',
            ['id'],
            'SET NULL',
            'CASCADE'
        );

        $this->addColumn('{{%lead_request}}', 'lr_source_id', $this->integer());
        $this->addForeignKey(
            'FK-lead_request-source_id',
            '{{%lead_request}}',
            ['lr_source_id'],
            '{{%sources}}',
            ['id'],
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-lead_request-source_id', '{{%lead_request}}');
        $this->dropColumn('{{%lead_request}}', 'lr_source_id');

        $this->dropForeignKey('FK-lead_request-project_id', '{{%lead_request}}');
        $this->dropColumn('{{%lead_request}}', 'lr_project_id');
    }
}
