<?php

use yii\db\Migration;

/**
 * Class m200416_153415_create_tbl_twilio_jwt_token
 */
class m200416_153415_create_tbl_twilio_jwt_token extends Migration
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

		$this->createTable('{{%twilio_jwt_token}}',	[
			'jt_id'                 => $this->primaryKey(),
			'jt_agent'              => $this->string(50)->notNull()->unique(),
			'jt_token'              => $this->text()->notNull(),
			'jt_app_sid'            => $this->text()->notNull(),
			'jt_expire_dt'          => $this->dateTime(),
			'jt_created_dt'         => $this->dateTime(),
			'jt_updated_dt'         => $this->dateTime(),

		], $tableOptions);

		$this->createIndex('IND-twilio_jwt_token_agent', '{{%twilio_jwt_token}}', ['jt_agent']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropTable('{{%twilio_jwt_token}}');
	}
}
