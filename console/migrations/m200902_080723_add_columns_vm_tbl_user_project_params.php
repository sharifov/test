<?php

use yii\db\Migration;

/**
 * Class m200902_080723_add_columns_vm_tbl_user_project_params
 */
class m200902_080723_add_columns_vm_tbl_user_project_params extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_project_params}}', 'upp_vm_enabled', $this->boolean());
        $this->addColumn('{{%user_project_params}}', 'upp_vm_user_status_id', $this->tinyInteger(1));
        $this->addColumn('{{%user_project_params}}', 'upp_vm_id', $this->integer());
        $this->addForeignKey('FK-upp-upp_vm_id', '{{%user_project_params}}', ['upp_vm_id'], '{{%user_voice_mail}}', ['uvm_id'], 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-upp-upp_vm_id', '{{%user_project_params}}');
        $this->addColumn('{{%user_project_params}}', 'upp_vm_id', $this->integer());
        $this->addColumn('{{%user_project_params}}', 'upp_vm_user_status_id', $this->tinyInteger(1));
        $this->addColumn('{{%user_project_params}}', 'upp_vm_enabled', $this->boolean());
    }
}
