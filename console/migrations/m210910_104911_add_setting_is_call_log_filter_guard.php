<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m210910_104911_add_setting_is_call_log_filter_guard
 */
class m210910_104911_add_setting_is_call_log_filter_guard extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('Enable');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'is_call_log_filter_guard',
                's_name' => 'Is Enable Call Log Filter Guard',
                's_type' => Setting::TYPE_BOOL,
                's_value' => false,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => 'Enable CallAntiSpam Service',
            ]
        );

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'is_call_log_filter_guard'
        ]]);
    }
}
