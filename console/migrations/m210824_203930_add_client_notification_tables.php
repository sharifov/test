<?php

use yii\db\Migration;

/**
 * Class m210824_203930_add_client_notification_tables
 */
class m210824_203930_add_client_notification_tables extends Migration
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

        $this->createTable('{{%client_notification}}', [
            'cn_id' => $this->primaryKey(),
            'cn_client_id' => $this->integer(),
            'cn_notification_type_id' => $this->tinyInteger(),
            'cn_object_id' => $this->integer(),
            'cn_communication_type_id' => $this->tinyInteger(),
            'cn_communication_object_id' => $this->integer(),
            'cn_created_dt' => $this->dateTime(),
            'cn_updated_dt' => $this->dateTime(),
        ], $tableOptions);

        // todo create indexes

        $this->addForeignKey(
            'FK-client_notification-client',
            '{{%client_notification}}',
            ['cn_client_id'],
            '{{%clients}}',
            ['id'],
            'CASCADE',
            'CASCADE'
        );

        $this->createTable('{{%client_notification_phone_list}}', [
            'cnfl_id' => $this->primaryKey(),
            'cnfl_status_id' => $this->tinyInteger(),
            'cnfl_from_phone_id' => $this->integer(),
            'cnfl_to_client_phone_id' => $this->integer(),
            'cnfl_start' => $this->dateTime(),
            'cnfl_end' => $this->dateTime(),
            'cnfl_message' => $this->text(),
            'cnfl_file_url' => $this->string(500),
            'cnfl_data_json' => $this->json(),
            'cnfl_call_sid' => $this->string(34),
            'cnfl_created_dt' => $this->dateTime(),
            'cnfl_updated_dt' => $this->dateTime(),
        ]);

        $this->createIndex('IND-client_notification_phone-status', '{{%client_notification_phone_list}}', ['cnfl_status_id']);
        $this->createIndex('IND-client_notification_phone-start', '{{%client_notification_phone_list}}', ['cnfl_start']);
        $this->createIndex('IND-client_notification_phone-end', '{{%client_notification_phone_list}}', ['cnfl_end']);
        $this->createIndex('IND-client_notification_phone-call_sid', '{{%client_notification_phone_list}}', ['cnfl_call_sid']);

        $this->addForeignKey(
            'FK-client_notification_phone-from_phone',
            '{{%client_notification_phone_list}}',
            ['cnfl_from_phone_id'],
            '{{%phone_list}}',
            ['pl_id'],
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-client_notification_phone-to_phone',
            '{{%client_notification_phone_list}}',
            ['cnfl_to_client_phone_id'],
            '{{%client_phone}}',
            ['id'],
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%client_notification_phone_list}}');
        $this->dropTable('{{%client_notification}}');
    }
}
