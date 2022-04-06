<?php

use yii\db\Migration;

/**
 * Class m220330_094328_create_tbl_email_quote
 */
class m220330_094328_create_tbl_email_quote extends Migration
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

        $this->createTable('{{%email_quote}}', [
            'eq_id' => $this->primaryKey(),
            'eq_email_id' => $this->integer()->notNull(),
            'eq_quote_id' => $this->integer()->notNull(),
            'eq_created_dt' => $this->timestamp(),
            'eq_created_by' => $this->integer()
        ], $tableOptions);

        $this->addForeignKey('FK-email_quote-eq_email_id', '{{%email_quote}}', 'eq_email_id', '{{%email}}', 'e_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-email_quote-eq_quote_id', '{{%email_quote}}', 'eq_quote_id', '{{%quotes}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-email_quote-eq_created_by', '{{%email_quote}}', 'eq_created_by', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');

        $this->createIndex('IND-email_quote-eq_email_id', '{{%email_quote}}', 'eq_email_id');
        $this->createIndex('IND-email_quote-eq_quote_id', '{{%email_quote}}', 'eq_quote_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%email_quote}}');
    }
}
