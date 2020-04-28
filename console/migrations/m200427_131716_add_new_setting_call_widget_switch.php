<?php

use yii\db\Migration;

/**
 * Class m200427_131716_add_new_setting_call_widget_switch
 */
class m200427_131716_add_new_setting_call_widget_switch extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->insert('{{%setting}}', [
			's_key' => 'use_new_web_phone_widget',
			's_name' => 'New WebPhone Widget',
			's_type' => \common\models\Setting::TYPE_BOOL,
			's_value' => 0,
			's_updated_dt' => date('Y-m-d H:i:s'),
			's_updated_user_id' => 1,
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
			'use_new_web_phone_widget'
		]]);

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}

		Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }

}
