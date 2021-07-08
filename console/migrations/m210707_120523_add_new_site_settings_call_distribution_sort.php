<?php

use yii\db\Migration;

/**
 * Class m210707_120523_add_new_site_settings_call_distribution_sort
 */
class m210707_120523_add_new_site_settings_call_distribution_sort extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = \common\models\SettingCategory::getOrCreateByName('Call');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'call_distribution_sort',
                's_name' => 'Call Distribution sort',
                's_description' => 'This option is used to set sorting in the query to find available users to call.',
                's_type' => \common\models\Setting::TYPE_ARRAY,
                's_value' => \frontend\helpers\JsonHelper::encode([
                    'general_line_call_count' => 'ASC',
                    'phone_ready_time' => 'ASC'
                ]),
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
            'call_distribution_sort',
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
