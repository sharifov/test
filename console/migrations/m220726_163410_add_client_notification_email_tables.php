<?php

use yii\db\Migration;

/**
 * Class m220726_163410_add_client_notification_email_tables
 */
class m220726_163410_add_client_notification_email_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%client_notification_email_list}}', [
            'cnel_id' => $this->primaryKey(),
            'cnel_status_id' => $this->tinyInteger(),
            'cnel_from_email_id' => $this->integer(),
            'cnel_name_from' => $this->string(),
            'cnel_to_client_email_id' => $this->integer(),
            'cnel_start' => $this->dateTime()->notNull(),
            'cnel_end' => $this->dateTime()->notNull(),
            'cnel_data_json' => $this->json(),
            'cnel_email_id' => $this->integer(),
            'cnel_created_dt' => $this->dateTime(),
            'cnel_updated_dt' => $this->dateTime(),
        ]);

        $this->createIndex('IND-client_notification_email_list-status', '{{%client_notification_email_list}}', ['cnel_status_id']);
        $this->createIndex('IND-client_notification_email_list-start', '{{%client_notification_email_list}}', ['cnel_start']);
        $this->createIndex('IND-client_notification_email_list-end', '{{%client_notification_email_list}}', ['cnel_end']);
        $this->createIndex('IND-client_notification_email_list-email_id', '{{%client_notification_email_list}}', ['cnel_email_id']);

        $this->addForeignKey(
            'FK-client_notification_email_list-from_email',
            '{{%client_notification_email_list}}',
            ['cnel_from_email_id'],
            '{{%email_list}}',
            ['el_id'],
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-client_notification_email_list-to_email',
            '{{%client_notification_email_list}}',
            ['cnel_to_client_email_id'],
            '{{%client_email}}',
            ['id'],
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-client_notification_email_list-email_id',
            '{{%client_notification_email_list}}',
            ['cnel_email_id'],
            '{{%email}}',
            ['e_id'],
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%client_notification_email_list}}');
    }
}
