<?php

use yii\db\Migration;

/**
 * Class m200616_101156_add_setting_lead_communication_new_call_widget
 */
class m200616_101156_add_setting_lead_communication_new_call_widget extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$this->insert('{{%setting}}', [
			's_key' => 'lead_communication_new_call_widget',
			's_name' => 'Lead communication block - init call in new phone widget',
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
			'lead_communication_new_call_widget'
		]]);

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}

		Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
	}
}
