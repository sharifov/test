<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m210128_114649_add_new_site_setting_quote_search_processing_fee
 */
class m210128_114649_add_new_site_setting_quote_search_processing_fee extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('General');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'quote_search_processing_fee',
                's_name' => 'Processing Fee for quote from search result',
                's_type' => Setting::TYPE_DOUBLE,
                's_value' => 0.00,
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
            'quote_search_processing_fee',
        ]]);
    }
}
