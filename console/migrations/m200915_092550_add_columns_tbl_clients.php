<?php

use yii\db\Migration;

/**
 * Class m200915_092550_add_columns_tbl_clients
 */
class m200915_092550_add_columns_tbl_clients extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%clients}}', 'cl_type_create', $this->tinyInteger(3));
        $this->addColumn('{{%clients}}', 'cl_project_id', $this->integer());
        $this->addForeignKey('FK-clients-cl_project_id', '{{%clients}}', ['cl_project_id'], '{{%projects}}', ['id'], 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-clients-cl_project_id', '{{%clients}}');
        $this->dropColumn('{{%clients}}', 'cl_project_id');
        $this->dropColumn('{{%clients}}', 'cl_type_create');
    }
}
