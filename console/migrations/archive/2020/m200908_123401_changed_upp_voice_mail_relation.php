<?php

use yii\db\Migration;

/**
 * Class m200908_123401_changed_upp_voice_mail_relation
 */
class m200908_123401_changed_upp_voice_mail_relation extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('FK-upp-upp_vm_id', '{{%user_project_params}}');
        $this->addForeignKey('FK-upp-upp_vm_id', '{{%user_project_params}}', ['upp_vm_id'], '{{%user_voice_mail}}', ['uvm_id'], 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-upp-upp_vm_id', '{{%user_project_params}}');
        $this->addForeignKey('FK-upp-upp_vm_id', '{{%user_project_params}}', ['upp_vm_id'], '{{%user_voice_mail}}', ['uvm_id'], 'CASCADE', 'CASCADE');
    }
}
