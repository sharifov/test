<?php

use yii\db\Migration;

/**
 * Class m210610_140802_create_tbl_client_chat_component_rule
 */
class m210610_140802_create_tbl_client_chat_component_rule extends Migration
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

        $this->createTable('{{%client_chat_component_rule}}', [
            'cccr_component_event_id' => $this->integer()->notNull(),
            'cccr_value' => $this->string(10)->notNull(),
            'cccr_runnable_component' => $this->tinyInteger()->unsigned()->notNull(),
            'cccr_component_config' => $this->json(),
            'cccr_sort_order' => $this->tinyInteger()->unsigned(),
            'cccr_enabled' => $this->boolean(),
            'cccr_created_user_id' => $this->integer(),
            'cccr_updated_user_id' => $this->integer(),
            'cccr_created_dt' => $this->dateTime(),
            'cccr_updated_dt' => $this->dateTime()
        ], $tableOptions);

        $this->addPrimaryKey('PK-client_chat_component_rule', '{{%client_chat_component_rule}}', [
            'cccr_component_event_id',
            'cccr_value',
            'cccr_runnable_component'
        ]);

        $this->addForeignKey('FK-client_chat_component_rule-component_event_id', '{{%client_chat_component_rule}}', 'cccr_component_event_id', '{{%client_chat_component_event}}', 'ccce_id', 'CASCADE', 'CASCADE');

        $this->addForeignKey('FK-client_chat_component_event-cccr_created_user_id', '{{%client_chat_component_rule}}', 'cccr_created_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-client_chat_component_event-cccr_updated_user_id', '{{%client_chat_component_rule}}', 'cccr_updated_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-client_chat_component_rule-component_event_id', '{{%client_chat_component_rule}}');
        $this->dropForeignKey('FK-client_chat_component_event-cccr_created_user_id', '{{%client_chat_component_rule}}');
        $this->dropForeignKey('FK-client_chat_component_event-cccr_updated_user_id', '{{%client_chat_component_rule}}');
        $this->dropTable('{{%client_chat_component_rule}}');
    }
}
