<?php

use yii\db\Migration;

/**
 * Class m181212_120000_create_tbl_sms
 */
class m181212_120000_create_tbl_sms extends Migration
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

        $this->createTable('{{%sms_template_type}}',	[
            'stp_id'                     => $this->primaryKey(),
            'stp_key'                    => $this->string(50)->unique()->notNull(),
            'stp_origin_name'            => $this->string(100)->notNull(),
            'stp_name'                   => $this->string(100)->notNull(),
            'stp_hidden'                 => $this->boolean()->defaultValue(false),
            'stp_created_user_id'        => $this->integer(),
            'stp_updated_user_id'        => $this->integer(),
            'stp_created_dt'             => $this->dateTime(),
            'stp_updated_dt'             => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey('FK-sms_template_type_stp_created_user_id', '{{%sms_template_type}}', ['stp_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-sms_template_type_stp_updated_user_id', '{{%sms_template_type}}', ['stp_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');


        $this->createTable('{{%sms}}',	[
            's_id'                     => $this->primaryKey(),
            's_reply_id'               => $this->integer(),
            's_lead_id'                => $this->integer(),
            's_project_id'             => $this->integer(),
            's_phone_from'             => $this->string()->notNull(),
            's_phone_to'               => $this->string(255)->notNull(),
            's_sms_text'               => $this->text(),
            's_sms_data'               => $this->text(),
            's_type_id'                => $this->smallInteger()->defaultValue(0), // 0 - DRAFT, 1 - OUT, 2 - INCOMING
            's_template_type_id'       => $this->integer(),
            's_language_id'            => $this->string(5),
            's_communication_id'       => $this->integer(),
            's_is_deleted'             => $this->boolean()->defaultValue(false),
            's_is_new'                 => $this->boolean()->defaultValue(false),
            's_delay'                  => $this->integer(),
            's_priority'               => $this->tinyInteger(1)->defaultValue(2),
            's_status_id'              => $this->tinyInteger(1)->defaultValue(1),
            's_status_done_dt'         => $this->dateTime(),
            's_read_dt'                => $this->dateTime(),
            's_error_message'          => $this->string(500),

            's_tw_price'                => $this->decimal(8, 5)->defaultValue(0),
            's_tw_sent_dt'              => $this->dateTime(),
            's_tw_account_sid'          => $this->string(40),
            's_tw_message_sid'          => $this->string(40),
            's_tw_num_segments'         => $this->smallInteger()->defaultValue(1),
            's_tw_to_country'           => $this->string(5),
            's_tw_to_state'             => $this->string(30),
            's_tw_to_city'              => $this->string(30),
            's_tw_to_zip'               => $this->string(10),
            's_tw_from_country'         => $this->string(5),
            's_tw_from_state'           => $this->string(30),
            's_tw_from_city'            => $this->string(30),
            's_tw_from_zip'             => $this->string(10),

            's_created_user_id'        => $this->integer(),
            's_updated_user_id'        => $this->integer(),
            's_created_dt'             => $this->dateTime(),
            's_updated_dt'             => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey('FK-sms_s_project_id', '{{%sms}}', ['s_project_id'], '{{%projects}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-sms_s_language_id', '{{%sms}}', ['s_language_id'], '{{%language}}', ['language_id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-sms_s_template_type_id', '{{%sms}}', ['s_template_type_id'], '{{%sms_template_type}}', ['stp_id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-sms_s_created_user_id', '{{%sms}}', ['s_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-sms_s_updated_user_id', '{{%sms}}', ['s_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-sms_s_lead_id', '{{%sms}}', ['s_lead_id'], '{{%leads}}', ['id'], 'SET NULL', 'CASCADE');

        $this->createIndex('IND-sms_s_tw_message_sid', '{{%sms}}', ['s_tw_message_sid']);
        $this->createIndex('IND-sms_s_communication_id', '{{%sms}}', ['s_communication_id']);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%sms}}');
        $this->dropTable('{{%sms_template_type}}');
    }


}
