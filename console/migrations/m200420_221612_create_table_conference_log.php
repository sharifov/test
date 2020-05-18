<?php

use yii\db\Migration;

/**
 * Class m200420_221612_create_table_conference_log
 */
class m200420_221612_create_table_conference_log extends Migration
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

		$this->createTable('{{%conference_log}}',	[
			'cl_id'                         => $this->primaryKey(),
			'cl_cf_sid'                     => $this->string(34)->notNull(),
			'cl_cf_id'                      => $this->integer()->notNull(),
			'cl_sequence_number'            => $this->smallInteger(),
			'cl_status_callback_event'      => $this->string(30),
			'cl_json_data'                  => $this->json(),
			'cl_created_dt'                 => $this->dateTime(),
		], $tableOptions);

		$this->addForeignKey('FK-conference_cl_cf_id', '{{%conference_log}}', ['cl_cf_id'], '{{%conference}}', ['cf_id'], 'CASCADE', 'CASCADE');
		$this->createIndex('IND-conference_cl_cf_sid', '{{%conference_log}}', ['cl_cf_sid']);
		$this->createIndex('IND-conference_cl_status_callback_event', '{{%conference_log}}', ['cl_status_callback_event']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropTable('{{%conference_log}}');
	}
}
