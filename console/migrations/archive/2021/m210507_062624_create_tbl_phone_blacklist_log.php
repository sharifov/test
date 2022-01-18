<?php

use yii\db\Migration;

/**
 * Class m210507_062624_create_tbl_phone_blacklist_log
 */
class m210507_062624_create_tbl_phone_blacklist_log extends Migration
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

        $this->createTable('{{%phone_blacklist_log}}', [
            'pbll_id' => $this->primaryKey(),
            'pbll_phone' => $this->string(30)->notNull(),
            'pbll_created_dt' => $this->dateTime(),
            'pbll_created_user_id' => $this->integer()
        ], $tableOptions);

        $this->createIndex('IDX-phone_blacklist_log-pbll_phone', '{{%phone_blacklist_log}}', 'pbll_phone');
        $this->addForeignKey('FK-phone_blacklist_log-pbll_created_user_id', '{{%phone_blacklist_log}}', 'pbll_created_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-phone_blacklist_log-pbll_created_user_id', '{{%phone_blacklist_log}}');
        $this->dropTable('{{%phone_blacklist_log}}');
    }
}
