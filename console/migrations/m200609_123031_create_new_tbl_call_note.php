<?php

use yii\db\Migration;

/**
 * Class m200609_123031_create_new_tbl_call_note
 */
class m200609_123031_create_new_tbl_call_note extends Migration
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

		$this->createTable('{{%call_note}}',	[
			'cn_id'              		=> $this->primaryKey(),
			'cn_call_id'               	=> $this->integer(),
			'cn_note'     				=> $this->string(255),
			'cn_created_dt'             => $this->dateTime(),
			'cn_updated_dt'             => $this->dateTime(),
			'cn_created_user_id'        => $this->integer(),
			'cn_updated_user_id'        => $this->integer(),
		], $tableOptions);

		$this->addForeignKey('FK-call_note-cn_call_id', '{{%call_note}}', ['cn_call_id'], '{{%call}}', ['c_id'], 'CASCADE', 'CASCADE');

		$this->addForeignKey('FK-call_note-cn_created_user_id', '{{%call_note}}', ['cn_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-call_note-cn_updated_user_id', '{{%call_note}}', ['cn_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropTable('{{%call_note}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }
}
