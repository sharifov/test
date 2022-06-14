<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;
use yii\helpers\Json;

/**
 * Class m220609_142112_add_setting_override_phone_to_forwarded_from
 */
class m220609_142112_add_setting_override_phone_to_forwarded_from extends Migration
{
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('Call');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'call_is_override_phone_to_forwarded_from',
                's_name' => 'Override phone to number to forwarder from number',
                's_type' => Setting::TYPE_BOOL,
                's_value' => false,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->delete('{{%setting}}', ['IN', 's_key', [
            'call_is_override_phone_to_forwarded_from',
        ]]);
    }
}
