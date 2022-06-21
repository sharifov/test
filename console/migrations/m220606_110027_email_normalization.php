<?php

use yii\db\Migration;

/**
 * Class m220606_110027_email_normalization
 */
class m220606_110027_email_normalization extends Migration
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

        $this->createTable('{{%email_address}}', [
            'ea_id' => $this->primaryKey(),
            'ea_email' => $this->string(160)->notNull(),
            'ea_name' => $this->string(100),
        ], $tableOptions);

        $this->createIndex('UNIQ-email', '{{%email_address}}', 'ea_email', true);

        $this->createTable('{{%email_body}}', [
            'embd_id' => $this->primaryKey(),
            'embd_email_subject' => $this->string(255),
            'embd_email_body_text' => 'mediumtext',
            'embd_email_data' => $this->json(),
            'embd_hash' => $this->string(32),
        ], $tableOptions);

        $this->createTable('{{%email_blob}}', [
            'embb_id' => $this->primaryKey(),
            'embb_body_id' => $this->integer(),
            'embb_email_body_blob' => 'MEDIUMBLOB',
        ], $tableOptions);

        $this->createIndex('IDX-embb_body_id', '{{%email_blob}}', 'embb_body_id');

        $this->createTable('{{%email_norm}}', [
            'e_id' => $this->primaryKey(),
            'e_project_id' => $this->integer(),
            'e_departament_id' => $this->integer(),
            'e_type_id' => $this->tinyInteger(1)->defaultValue(0)->notNull(),
            'e_is_deleted' => $this->boolean()->defaultValue(false)->notNull(),
            'e_status_id' => $this->tinyInteger(1)->defaultValue(1)->notNull(),
            'e_created_user_id' => $this->integer(),
            'e_created_dt' => $this->dateTime(),
            'e_updated_dt' => $this->dateTime(),
            'e_body_id' => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey('FK-e_project_id', '{{%email_norm}}', ['e_project_id'], '{{%projects}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-e_departament_id', '{{%email_norm}}', ['e_departament_id'], '{{%department}}', ['dep_id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-e_created_user_id', '{{%email_norm}}', ['e_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');


        $this->createTable('{{%email_params}}', [
            'ep_id' => $this->primaryKey(),
            'ep_email_id' => $this->integer(),
            'ep_template_type_id' => $this->integer(),
            'ep_language_id' => $this->string(5),
            'ep_priority' => $this->tinyInteger(1)->defaultValue(2)->notNull(),
        ], $tableOptions);

        $this->addForeignKey('FK-ep_template_type_id', '{{%email_params}}', ['ep_template_type_id'], '{{%email_template_type}}', ['etp_id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-ep_language_id', '{{%email_params}}', ['ep_language_id'], '{{%language}}', ['language_id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-ep_email_id', '{{%email_params}}', ['ep_email_id'], '{{%email_norm}}', 'e_id', 'CASCADE', 'CASCADE');

        $this->createTable('{{%email_log}}', [
            'el_id' => $this->primaryKey(),
            'el_email_id' => $this->integer(),
            'el_status_done_dt' => $this->dateTime(),
            'el_read_dt' => $this->dateTime(),
            'el_error_message' => $this->string(500),
            'el_message_id' => $this->string(500),
            'el_ref_message_id' => 'mediumtext',
            'el_inbox_created_dt' => $this->dateTime(),
            'el_inbox_email_id' => $this->integer(),
            'el_communication_id' => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey('FK-el_email_id', '{{%email_log}}', ['el_email_id'], '{{%email_norm}}', 'e_id', 'CASCADE', 'CASCADE');

        $this->createTable('{{%email_contact}}', [
            'ec_id' => $this->primaryKey(),
            'ec_email_id' => $this->integer(),
            'ec_address_id' => $this->integer(),
            'ec_type_id' => $this->tinyInteger(1)->defaultValue(2)->notNull(),
        ]);

        $this->addForeignKey('FK-ec_email_id', '{{%email_contact}}', ['ec_email_id'], '{{%email_norm}}', 'e_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-ec_address_id', '{{%email_contact}}', ['ec_address_id'], '{{%email_address}}', 'ea_id', 'CASCADE', 'CASCADE');
        $this->createIndex('UNIQ-email-address-type', '{{%email_contact}}', ['ec_email_id', 'ec_address_id', 'ec_type_id'], true);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%email_log}}');
        $this->dropTable('{{%email_contact}}');
        $this->dropTable('{{%email_params}}');
        $this->dropTable('{{%email_address}}');
        $this->dropTable('{{%email_blob}}');
        $this->dropTable('{{%email_body}}');
        $this->dropTable('{{%email_norm}}');
    }
}
