<?php

use yii\db\Migration;

/**
 * Class m200917_085723_change_fk_parent_id_tbl_call_command
 */
class m200917_085723_change_fk_parent_id_tbl_call_command extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('FK-call_command-ccom_parent_id', '{{%call_command}}');

        $this->addForeignKey('FK-call_command-ccom_parent_id', '{{%call_command}}', ['ccom_parent_id'],
        '{{%call_command}}', ['ccom_id'], 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-call_command-ccom_parent_id', '{{%call_command}}');

        $this->addForeignKey('FK-call_command-ccom_parent_id', '{{%call_command}}', ['ccom_parent_id'],
        '{{%call_command}}', ['ccom_id'], 'CASCADE', 'CASCADE');
    }
}
