<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m220627_083113_rename_site_settings_property_two_factor_counter
 */
class m220627_083113_rename_site_settings_property_two_factor_counter extends Migration
{
    public const CATEGORY_NAME_TWO_FACTOR_AUTH = 'Two factor auth';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->delete('{{%setting}}', [
             's_key' => 'two_factor_counter',
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'two_factor_attempts_settings',
            's_name' => 'Two factor auth attempts settings',
            's_type' => Setting::TYPE_ARRAY,
            's_value' => json_encode([
                'max_attempts' => 7,
                'show_warning_attempts_remain' => 3,
            ]),
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => $this->getSettingCategoryId(self::CATEGORY_NAME_TWO_FACTOR_AUTH),
        ]);

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', [
            's_key' => 'two_factor_attempts_settings',
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'two_factor_counter',
            's_name' => 'Two factor auth counter',
            's_type' => Setting::TYPE_INT,
            's_value' => 60,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => $this->getSettingCategoryId(self::CATEGORY_NAME_TWO_FACTOR_AUTH),
        ]);

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }

    /**
     * @param string $categoryName
     * @return int
     */
    public function getSettingCategoryId(string $categoryName): int
    {
        $settingCategory = SettingCategory::getOrCreateByName($categoryName);
        return $settingCategory->sc_id;
    }
}
