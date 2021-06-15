<?php

use yii\db\Migration;

/**
 * Class m210614_140350_create_tbl_client_chat_visitor_subscription
 */
class m210614_140350_create_tbl_client_chat_visitor_subscription extends Migration
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

        $this->createTable('{{%visitor_subscription}}', [
            'vs_id' => $this->primaryKey(),
            'vs_subscription_uid' => $this->string(100)->notNull(),
            'vs_type_id' => $this->tinyInteger()->unsigned()->notNull(),
            'vs_enabled' => $this->boolean()->defaultValue(true),
            'vs_expired_date' => $this->date(),
            'vs_created_dt' => $this->dateTime(),
            'vs_updated_dt' => $this->dateTime()
        ], $tableOptions);

        $this->createIndex('UQ-visitor_subscription-vs_subscription_uid_vs_type_id', '{{%visitor_subscription}}', [
            'vs_subscription_uid', 'vs_type_id'
        ], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%visitor_subscription}}');
    }
}
