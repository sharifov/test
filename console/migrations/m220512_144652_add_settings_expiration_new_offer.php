<?php

use yii\db\Migration;

/**
 * Class m220512_144652_add_settings_expiration_new_offer
 */
class m220512_144652_add_settings_expiration_new_offer extends Migration
{
    public const TABLE_SETTING = '{{%setting}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = \common\models\SettingCategory::getOrCreateByName('Product Quote');

        $this->insert(self::TABLE_SETTING, [
            's_key' => 'expiration_days_of_new_offers',
            's_name' => 'Expiration days of new offers',
            's_type' => 'int',
            's_value' => 7,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => $settingCategory->sc_id,
            's_description' => 'Expiration days of new offers (VE, VR and SC)',
        ]);

        $this->insert(self::TABLE_SETTING, [
            's_key' => 'minimum_hours_difference_between_offers',
            's_name' => 'Minimum hours difference between offers',
            's_type' => 'int',
            's_value' => 24,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => $settingCategory->sc_id,
            's_description' => 'Minimum hours difference between new and current offers (VE, VR and SC)',
        ]);

        if (Yii::$app->cache) {
            Yii::$app->cache->delete('site_settings');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete(self::TABLE_SETTING, ['s_key' => 'expiration_days_of_new_offers']);
        $this->delete(self::TABLE_SETTING, ['s_key' => 'minimum_hours_difference_between_offers']);
    }
}
