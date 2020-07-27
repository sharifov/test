<?php

use yii\db\Migration;

/**
 * Class m200715_122417_add_new_site_setting
 */
class m200715_122417_add_new_site_setting extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->insert('{{%setting}}', [
			's_key' => 'cc_sound_notification_enable',
			's_name' => 'Enable sound notification on incoming message',
			's_type' => \common\models\Setting::TYPE_BOOL,
			's_value' => 0,
			's_updated_dt' => date('Y-m-d H:i:s'),
			's_category_id' => null,
		]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->delete('{{%setting}}', ['s_key' => 'cc_sound_notification_enable']);
	}
}
