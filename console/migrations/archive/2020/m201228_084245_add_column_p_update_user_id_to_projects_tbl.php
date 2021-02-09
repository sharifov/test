<?php

use yii\db\Migration;

/**
 * Class m201228_084245_add_column_p_update_user_id_to_projects_tbl
 */
class m201228_084245_add_column_p_update_user_id_to_projects_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%projects}}', 'p_update_user_id', $this->integer());
        $this->addForeignKey('FK-projects-p_update_user_id', '{{%projects}}', ['p_update_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-projects-p_update_user_id', '{{%projects}}');
        $this->dropColumn('{{%projects}}', 'p_update_user_id');
    }
}
