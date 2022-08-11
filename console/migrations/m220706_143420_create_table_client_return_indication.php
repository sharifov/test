<?php

use yii\db\Migration;

/**
 * Class m220706_143420_create_table_client_return_indication
 */
class m220706_143420_create_table_client_return_indication extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%client_user_return}}', [
            'cur_client_id' => $this->integer()->notNull(),
            'cur_user_id' => $this->integer()->notNull(),
            'cur_created_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-client_user_return', '{{%client_user_return}}', ['cur_client_id', 'cur_user_id']);
        $this->addForeignKey('FK-client_user_return-cur_client_id', '{{%client_user_return}}', 'cur_client_id', '{{%clients}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-client_user_return-cur_user_id', '{{%client_user_return}}', 'cur_user_id', '{{%employees}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%client_user_return}}');
    }
}
