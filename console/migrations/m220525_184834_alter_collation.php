<?php

use yii\db\Migration;

/**
 * Class m220525_184834_alter_collation
 */
class m220525_184834_alter_collation extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tables = [
                    'client_notification_phone_list',
                    'client_notification_sms_list',
                    'client_chat_survey_response',
                    'phone_device',
                    'phone_device_log',
                    'user_shift_schedule_log'
        ];
        $this->execute("SET foreign_key_checks = 0;");
        foreach ($tables as $tableName) {
            $this->execute("ALTER TABLE `$tableName` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        }
        $this->execute("SET foreign_key_checks = 1;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }
}
