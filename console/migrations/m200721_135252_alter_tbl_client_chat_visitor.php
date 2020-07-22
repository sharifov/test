<?php

use yii\db\Migration;

/**
 * Class m200721_135252_alter_tbl_client_chat_visitor
 */
class m200721_135252_alter_tbl_client_chat_visitor extends Migration
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

		$this->createTable('{{%client_chat_visitor_data}}', [
			'cvd_id' => $this->primaryKey(),
			'cvd_ccv_id' => $this->integer()->unique(),
			'cvd_country' => $this->string(50),
			'cvd_region' => $this->string(5),
			'cvd_city' => $this->string(50),
			'cvd_latitude' => $this->float(),
			'cvd_longitude' => $this->float(),
			'cvd_url' => $this->string(),
			'cvd_title' => $this->string(50),
			'cvd_referrer' => $this->string(),
			'cvd_timezone' => $this->string(50),
			'cvd_local_time' => $this->string(10),
			'cvd_data' => $this->json(),
			'cvd_created_dt' => $this->dateTime(),
			'cvd_updated_dt' => $this->dateTime()
		], $tableOptions);

		$this->addForeignKey('FK-client_chat_visitor_data-cvd_ccv_id', '{{%client_chat_visitor_data}}', ['cvd_ccv_id'], '{{%client_chat_visitor}}', ['ccv_id'], 'SET NULL', 'CASCADE');

		$this->dropForeignKey('FK-visitor_log-vl_cch_id', '{{%visitor_log}}');

		$this->dropColumn('{{%visitor_log}}', 'vl_cch_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->dropForeignKey('FK-client_chat_visitor_data-cvd_ccv_id', '{{%client_chat_visitor_data}}');

    	$this->dropTable('{{%client_chat_visitor_data}}');

    	$this->addColumn('{{%visitor_log}}', 'vl_cch_id', $this->integer());

    	$this->addForeignKey('FK-visitor_log-vl_cch_id', '{{%visitor_log}}', ['vl_cch_id'], '{{client_chat}}', ['cch_id'], 'SET NULL', 'CASCADE');
    }
}
