<?php

use common\models\Setting;
use common\models\SettingCategory;
use frontend\helpers\JsonHelper;
use yii\db\Migration;

/**
 * Class m210926_073710_add_new_site_setting_call_spam_filter
 */
class m210926_073710_add_new_site_setting_call_spam_filter extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('Call');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'call_spam_filter',
                's_name' => 'Client notification start interval',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => JsonHelper::encode([
                    'enabled' => false,
                    'rate' => 0.3567,
                    'message' => '',
                    'redialEnabled' => false,
                ]),
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
            'call_spam_filter',
        ]]);
    }
}
