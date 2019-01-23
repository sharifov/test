<?php

use yii\db\Migration;

/**
 * Class m190123_105515_create_tbl_call
 */
class m190123_105515_create_tbl_call extends Migration
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

        //https://www.twilio.com/docs/voice/api/call#statuscallback

        $this->createTable('{{%call}}',	[
            'c_id'                 => $this->primaryKey(),
            'c_call_sid'           => $this->string(34)->notNull(),
            'c_account_sid'        => $this->string(34)->notNull(),
            'c_call_type_id'       => $this->tinyInteger(1),
            'c_from'               => $this->string(100),
            'c_to'                 => $this->string(100),
            'c_sip'                => $this->string(100),
            'c_call_status'        => $this->string(15),
            'c_api_version'        => $this->string(10),
            'c_direction'          => $this->string(15),
            'c_forwarded_from'     => $this->string(100),
            'c_caller_name'        => $this->string(50),
            'c_parent_call_sid'    => $this->string(34),
            'c_call_duration'      => $this->string(20),
            'c_sip_response_code'  => $this->string(10),
            'c_recording_url'      => $this->string(120),
            'c_recording_sid'      => $this->string(34),
            'c_recording_duration' => $this->string(20),
            'c_timestamp'          => $this->string(40),
            'c_uri'                => $this->string(120),
            'c_sequence_number'    => $this->string(40),
            'c_lead_id'            => $this->integer(),
            'c_created_user_id'    => $this->integer(),
            'c_created_dt'         => $this->dateTime(),

        ], $tableOptions);

        $this->createIndex('IND-call_c_call_sid', '{{%call}}', ['c_call_sid']);
        $this->createIndex('IND-call_c_from', '{{%call}}', ['c_from']);
        $this->createIndex('IND-call_c_to', '{{%call}}', ['c_to']);
        $this->createIndex('IND-call_c_lead_id', '{{%call}}', ['c_lead_id']);

        $this->addForeignKey('FK-call_c_created_user_id', '{{%call}}', ['c_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-call_c_lead_id', '{{%call}}', ['c_lead_id'], '{{%leads}}', ['id'], 'SET NULL', 'CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%call}}');
    }


}
