<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m210622_073707_add_setting_enable_check_phone_neutrino
 */
class m210622_073707_add_setting_enable_check_phone_neutrino extends Migration
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
                's_key' => 'enable_check_phone_by_neutrino',
                's_name' => 'Enable check phone by Neutrino',
                's_type' => Setting::TYPE_BOOL,
                's_value' => false,
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
            'enable_check_phone_by_neutrino',
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
