<?php

use yii\db\Migration;

/**
 * Class m211204_153509_add_phone_device_log
 */
class m211204_153509_add_phone_device_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%phone_device}}', [
            'pd_id' => $this->primaryKey(),
            'pd_user_id' => $this->integer()->notNull(),
            'pd_connection_id' => $this->bigInteger(),
            'pd_name' => $this->string(255)->notNull(),
            'pd_device_identity' => $this->string(255)->notNull(),
            'pd_status_device' => $this->boolean(),
            'pd_status_speaker' => $this->boolean(),
            'pd_status_microphone' => $this->boolean(),
            'pd_ip_address' => $this->string(45),
            'pd_user_agent' => $this->string(500),
            'pd_created_dt' => $this->dateTime()->notNull(),
            'pd_updated_dt' => $this->dateTime()->notNull(),
        ]);
        $this->createIndex('IDX-phone_device_identity', '{{%phone_device}}', ['pd_device_identity'], true);
        $this->addForeignKey(
            'FK-phone_device_user',
            '{{%phone_device}}',
            ['pd_user_id'],
            '{{%employees}}',
            ['id'],
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-phone_device_connection',
            '{{%phone_device}}',
            ['pd_connection_id'],
            '{{%user_connection}}',
            ['uc_id'],
            'SET NULL',
            'CASCADE'
        );

        $this->createTable('{{%phone_device_log}}', [
            'pdl_id' => $this->primaryKey(),
            'pdl_user_id' => $this->integer(),
            'pdl_device_id' => $this->integer(),
            'pdl_level' => $this->tinyInteger(1)->notNull(),
            'pdl_message' => $this->string(255)->notNull(),
            'pdl_error' => $this->json(),
            'pdl_stacktrace' => $this->text(),
            'pdl_timestamp_dt' => $this->dateTime()->notNull(),
            'pdl_created_dt' => $this->dateTime()->notNull()
        ]);
        $this->createIndex('IDX-phone_device_log_user_ts', '{{%phone_device_log}}', ['pdl_user_id', 'pdl_timestamp_dt']);
        $this->addForeignKey(
            'FK-phone_device_log_device',
            '{{%phone_device_log}}',
            ['pdl_device_id'],
            '{{%phone_device}}',
            ['pd_id'],
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%phone_device_log}}');
        $this->dropTable('{{%phone_device}}');
    }
}
