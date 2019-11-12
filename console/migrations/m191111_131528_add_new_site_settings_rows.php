<?php

use yii\db\Migration;

/**
 * Class m191111_131528_add_new_site_settings_rows
 */
class m191111_131528_add_new_site_settings_rows extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$setting = new \common\models\Setting();

		$testPhones = [];

		$setting->s_key = 'test_phone_list';
		$setting->s_name = 'Test Phone List';
		$setting->s_type = 'array';
		$setting->s_value = json_encode($testPhones);

		if (!$setting->save()) {
			echo 'Test phone list is not saved' . PHP_EOL;
			return false;
		}

		$setting = new \common\models\Setting();

		$allowedIp = [];

		$setting->s_key = 'test_allow_ip_address_list';
		$setting->s_name = 'Allowed Ip List';
		$setting->s_type = 'array';
		$setting->s_value = json_encode($allowedIp);

		if (!$setting->save()) {
			echo 'Test phone list is not saved' . PHP_EOL;
			return false;
		}
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$testPhonesSetting = \common\models\Setting::findOne(['s_key' => 'test_phone_list']);

    	if ($testPhonesSetting) {
			$testPhonesSetting->delete();
		}

    	$allowedIpSetting = \common\models\Setting::findOne(['s_key' => 'test_allow_ip_address_list']);

    	if ($allowedIpSetting) {
    		$allowedIpSetting->delete();
		}
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191111_131528_add_new_site_settings_rows cannot be reverted.\n";

        return false;
    }
    */
}
