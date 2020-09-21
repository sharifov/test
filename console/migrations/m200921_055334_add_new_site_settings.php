<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m200921_055334_add_new_site_settings
 */
class m200921_055334_add_new_site_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->insert('{{%setting}}', [
			's_key' => 'rc_username_for_register_channel',
			's_name' => 'From what name the client chat channel will be registered in Rocket Chat',
			's_type' => Setting::TYPE_STRING,
			's_value' => 'bot',
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
			'rc_username_for_register_channel'
		]]);

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}

		Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }

}
