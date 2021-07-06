<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m210706_115446_add_new_site_setting_call_duration_seconds_gl_count
 */
class m210706_115446_add_new_site_setting_call_duration_seconds_gl_count extends Migration
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
                's_key' => 'call_duration_seconds_gl_count',
                's_name' => 'Call Duration in seconds',
                's_description' => 'This option is used to increase the total counter of the general line call for each agent',
                's_type' => Setting::TYPE_INT,
                's_value' => 20,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
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
            'call_duration_seconds_gl_count',
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
