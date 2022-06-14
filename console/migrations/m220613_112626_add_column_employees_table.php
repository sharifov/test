<?php

use yii\db\Migration;

/**
 * Class m220613_112626_add_column_employees_table
 */
class m220613_112626_add_column_employees_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%employees}}', 'e_created_user_id', $this->integer());
        $this->addColumn('{{%employees}}', 'e_updated_user_id', $this->integer());
        $this->addColumn('{{%employees}}', 'last_login_dt', $this->dateTime());

        $this->addForeignKey('FK-employees-e_created_user_id', '{{%employees}}', 'e_created_user_id', '{{%employees}}', 'id', 'SET NULL');
        $this->addForeignKey('FK-employees-e_updated_user_id', '{{%employees}}', 'e_updated_user_id', '{{%employees}}', 'id', 'SET NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-employees-e_created_user_id', '{{%employees}}');
        $this->dropForeignKey('FK-employees-e_updated_user_id', '{{%employees}}');

        $this->dropColumn('{{%employees}}', 'e_created_user_id');
        $this->dropColumn('{{%employees}}', 'e_updated_user_id');
        $this->dropColumn('{{%employees}}', 'last_login_dt');
    }
}
