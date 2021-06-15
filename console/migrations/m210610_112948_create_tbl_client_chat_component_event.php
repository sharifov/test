<?php

use yii\db\Migration;

/**
 * Class m210610_112948_create_tbl_client_chat_component_event
 */
class m210610_112948_create_tbl_client_chat_component_event extends Migration
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

        $this->createTable('{{%client_chat_component_event}}', [
            'ccce_id' => $this->primaryKey(),
            'ccce_chat_channel_id' => $this->integer(),
            'ccce_component' => $this->tinyInteger()->unsigned()->notNull(),
            'ccce_event_type' => $this->tinyInteger(1)->notNull(),
            'ccce_component_config' => $this->json(),
            'ccce_enabled' => $this->boolean(),
            'ccce_sort_order' => $this->tinyInteger()->unsigned(),
            'ccce_created_user_id' => $this->integer(),
            'ccce_updated_user_id' => $this->integer(),
            'ccce_created_dt' => $this->dateTime(),
            'ccce_updated_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey('FK-client_chat_component_event-ccce_chat_channel_id', '{{%client_chat_component_event}}', 'ccce_chat_channel_id', '{{%client_chat_channel}}', 'ccc_id', 'CASCADE', 'CASCADE');
        $this->createIndex('UQ-client_chat_component_event-cch_id-component-event_type', '{{%client_chat_component_event}}', [
            'ccce_chat_channel_id',
            'ccce_component',
            'ccce_event_type'
        ], true);

        $this->addForeignKey('FK-client_chat_component_event-ccce_created_user_id', '{{%client_chat_component_event}}', 'ccce_created_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-client_chat_component_event-ccce_updated_user_id', '{{%client_chat_component_event}}', 'ccce_updated_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-client_chat_component_event-ccce_chat_channel_id', '{{%client_chat_component_event}}');
        $this->dropForeignKey('FK-client_chat_component_event-ccce_created_user_id', '{{%client_chat_component_event}}');
        $this->dropForeignKey('FK-client_chat_component_event-ccce_updated_user_id', '{{%client_chat_component_event}}');
        $this->dropIndex('UQ-client_chat_component_event-cch_id-component-event_type', '{{%client_chat_component_event}}');
        $this->dropTable('{{%client_chat_component_event}}');
    }
}
