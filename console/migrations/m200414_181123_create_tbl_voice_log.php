<?php

use yii\db\Migration;

/**
 * Class m200414_181123_create_tbl_voice_log
 */
class m200414_181123_create_tbl_voice_log extends Migration
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

		$this->createTable('{{%voice_log}}',	[
			'vl_id'                 => $this->primaryKey(),
			'vl_call_sid'           => $this->string(34)->notNull(),
			'vl_account_sid'        => $this->string(34)->notNull(),
			'vl_from'               => $this->string(100),
			'vl_to'                 => $this->string(100),
			'vl_call_status'        => $this->string(15),
			'vl_api_version'        => $this->string(10),
			'vl_direction'          => $this->string(15),
			'vl_forwarded_from'     => $this->string(100),
			'vl_caller_name'        => $this->string(50),
			'vl_parent_call_sid'    => $this->string(34),
			'vl_call_duration'      => $this->string(20),
			'vl_sip_response_code'  => $this->string(10),
			'vl_recording_url'      => $this->string(200),
			'vl_recording_sid'      => $this->string(34),
			'vl_recording_duration' => $this->string(20),
			'vl_timestamp'          => $this->string(40),
			'vl_callback_source'    => $this->string(40),
			'vl_sequence_number'    => $this->string(40),
			'vl_created_dt'         => $this->dateTime(),
		], $tableOptions);

		$this->createIndex('IND-voice_log_vl_call_sid', '{{%voice_log}}', ['vl_call_sid']);
		$this->createIndex('IND-voice_log_vl_parent_call_sid', '{{%voice_log}}', ['vl_parent_call_sid']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropIndex('IND-voice_log_vl_call_sid', '{{%voice_log}}');
		$this->dropIndex('IND-voice_log_vl_parent_call_sid', '{{%voice_log}}');
		$this->dropTable('{{%voice_log}}');
	}
}
