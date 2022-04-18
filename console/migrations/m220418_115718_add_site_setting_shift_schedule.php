<?php

use yii\db\Migration;

/**
 * Class m220418_115718_add_site_setting_shift_schedule
 */
class m220418_115718_add_site_setting_shift_schedule extends Migration
{
    public function safeUp()
    {
        try {
            $settingCategory = \common\models\SettingCategory::getOrCreateByName('Shift Schedule');

            $this->insert(
                '{{%setting}}',
                [
                    's_key'         => 'shift_schedule',
                    's_name'        => 'Shift Schedule',
                    's_type'        => \common\models\Setting::TYPE_ARRAY,
                    's_value'       => json_encode([
                            'generate_enabled'        => true,
                            'days_limit'              => 20,
                            'days_offset'             => 0,
                        ]),
                    's_updated_dt'  => date('Y-m-d H:i:s'),
                    's_category_id' => $settingCategory->sc_id,
                ]
            );

            if (Yii::$app->cache) {
                Yii::$app->cache->delete('site_settings');
            }
        } catch (Throwable $throwable) {
            Yii::error(
                $throwable,
                'm220418_115718_add_site_setting_shift_schedule:safeUp:Throwable'
            );
        }
    }


    public function safeDown()
    {
        $this->delete('{{%setting}}', [
            'IN',
            's_key',
            [
                'shift_schedule',
            ]
        ]);

        if (Yii::$app->cache) {
            Yii::$app->cache->delete('site_settings');
        }
    }
}
