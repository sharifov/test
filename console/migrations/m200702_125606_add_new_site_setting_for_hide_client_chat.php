<?php

use yii\db\Migration;

/**
 * Class m200702_125606_add_new_site_setting_for_hide_client_chat
 */
class m200702_125606_add_new_site_setting_for_hide_client_chat extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$this->insert('{{%setting}}', [
			's_key' => 'enable_client_chat',
			's_name' => 'Enable client chat',
			's_type' => \common\models\Setting::TYPE_BOOL,
			's_value' => 0,
			's_updated_dt' => date('Y-m-d H:i:s'),
			's_category_id' => null
		]);

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}

		Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$this->delete('{{%setting}}', ['IN', 's_key', [
			'enable_client_chat'
		]]);

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}

		Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
	}
}
