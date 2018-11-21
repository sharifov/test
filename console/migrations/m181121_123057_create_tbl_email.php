<?php

use yii\db\Migration;

/**
 * Class m181121_123057_create_tbl_email
 */
class m181121_123057_create_tbl_email extends Migration
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


        $this->createTable('{{%email_template_type}}',	[
            'etp_id'                     => $this->primaryKey(),
            'etp_key'                    => $this->string(50)->unique()->notNull(),
            'etp_name'                   => $this->string(100)->notNull(),
            'etp_created_user_id'        => $this->integer(),
            'etp_updated_user_id'        => $this->integer(),
            'etp_created_dt'             => $this->dateTime(),
            'etp_updated_dt'             => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey('FK-email_template_type_etp_created_user_id', '{{%email_template_type}}', ['etp_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-email_template_type_etp_updated_user_id', '{{%email_template_type}}', ['etp_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');


        $this->createTable('{{%email}}',	[
            'e_id'                     => $this->primaryKey(),
            'e_reply_id'               => $this->integer(),
            'e_lead_id'                => $this->integer(),
            'e_project_id'             => $this->integer(),
            'e_email_from'             => $this->string()->notNull(),
            'e_email_to'               => $this->string(255)->notNull(),
            'e_email_cc'               => $this->string(255),
            'e_email_bc'               => $this->string(255),
            'e_email_subject'          => $this->string(255),
            'e_email_body_html'        => $this->text(),
            'e_email_body_text'        => $this->text(),
            'e_attach'                 => $this->string(255),
            'e_email_data'             => $this->text(),
            'e_type_id'                => $this->smallInteger()->defaultValue(0), // 0 - DRAFT, 1 - OUT, 2 - INCOMING
            'e_template_type_id'       => $this->integer(),
            'e_language_id'            => $this->string(5),
            'e_communication_id'       => $this->integer(),
            //'e_is_draft'               => $this->boolean()->defaultValue(false),
            'e_is_deleted'             => $this->boolean()->defaultValue(false),
            'e_is_new'                 => $this->boolean()->defaultValue(false),
            'e_delay'                  => $this->integer(),
            'e_priority'               => $this->tinyInteger(1)->defaultValue(2),
            'e_status_id'              => $this->tinyInteger(1)->defaultValue(1),
            'e_status_done_dt'         => $this->dateTime(),
            'e_read_dt'                => $this->dateTime(),
            'e_error_message'          => $this->string(500),
            'e_created_user_id'        => $this->integer(),
            'e_updated_user_id'        => $this->integer(),
            'e_created_dt'             => $this->dateTime(),
            'e_updated_dt'             => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey('FK-email_e_project_id', '{{%email}}', ['e_project_id'], '{{%projects}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-email_e_language_id', '{{%email}}', ['e_language_id'], '{{%language}}', ['language_id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-email_e_template_type_id', '{{%email}}', ['e_template_type_id'], '{{%email_template_type}}', ['etp_id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-email_e_created_user_id', '{{%email}}', ['e_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-email_e_updated_user_id', '{{%email}}', ['e_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-email_e_lead_id', '{{%email}}', ['e_lead_id'], '{{%leads}}', ['id'], 'SET NULL', 'CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%email}}');
        $this->dropTable('{{%email_type}}');
    }


}
