<?php

namespace modules\email\migrations;

use yii\db\Migration;

/**
 * Class m200419_190416_create_email_account_tbl
 */
class m200419_190416_create_email_account_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%email_account}}', [
            'ea_id' => $this->primaryKey(),
            'ea_email' => $this->string(160)->unique()->notNull(),
            'ea_imap_settings' => $this->text(),
            'ea_gmail_command' => $this->string(20),
            'ea_gmail_token' => $this->text(),
            'ea_protocol' => $this->tinyInteger(),
            'ea_options' => $this->text(),
            'ea_active' => $this->boolean()->defaultValue(true),
            'ea_created_user_id' => $this->integer(),
            'ea_updated_user_id' => $this->integer(),
            'ea_created_dt' => $this->dateTime(),
            'ea_updated_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey('FK-email_account-ea_created_user_id', '{{%email_account}}', ['ea_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-email_account-ea_updated_user_id', '{{%email_account}}', ['ea_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-email_account-ea_created_user_id', '{{%email_account}}');
        $this->dropForeignKey('FK-email_account-ea_updated_user_id', '{{%email_account}}');
        $this->dropTable('{{%email_account}}');
    }
}
