<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m220127_113500_add_setting_lead_redial_exclude
 */
class m220127_113500_add_setting_lead_redial_exclude extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'redial_lead_exclude_attributes'
        ]]);

        $settingCategory = SettingCategory::getOrCreateByName('Redial');
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'redial_lead_exclude_attributes',
                's_name' => 'Redial lead exclude attributes',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => json_encode([
                    'projects' => [],
                    'departments' => [],
                    'cabins' => [],
                    'hasFlightDetails' => false,
                    'isTest' => true,
                ]),
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => 'Redial lead exclude attributes',
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
            'redial_lead_exclude_attributes',
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
