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
            'FK-phone_device_log_user',
            '{{%phone_device_log}}',
            ['pdl_user_id'],
            '{{%employees}}',
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
        $this->dropTable('{{%phone_device_log}}');
    }
}
