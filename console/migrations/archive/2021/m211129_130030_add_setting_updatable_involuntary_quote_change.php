<?php

use common\models\Setting;
use common\models\SettingCategory;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use yii\db\Migration;

/**
 * Class m211129_130030_add_setting_updatable_involuntary_quote_change
 */
class m211129_130030_add_setting_updatable_involuntary_quote_change extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('Product Quote Change');
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'updatable_involuntary_quote_change',
                's_name' => 'Updatable involuntary quote change',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => json_encode([
                    ProductQuoteChangeStatus::NEW => 'NEW',
                    ProductQuoteChangeStatus::PENDING => 'PENDING',
                    ProductQuoteChangeStatus::ERROR => 'ERROR',
                ]),
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => null,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'updatable_involuntary_quote_change',
        ]]);
    }
}
