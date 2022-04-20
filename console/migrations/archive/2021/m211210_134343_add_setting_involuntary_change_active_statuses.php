<?php

use common\models\Setting;
use common\models\SettingCategory;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use yii\db\Migration;

/**
 * Class m211210_134343_add_setting_involuntary_change_active_statuses
 */
class m211210_134343_add_setting_involuntary_change_active_statuses extends Migration
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
                's_key' => 'involuntary_change_active_statuses',
                's_name' => 'Involuntary Change Active Statuses',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => json_encode([
                    ProductQuoteChangeStatus::CONFIRMED => 'CONFIRMED',
                    ProductQuoteChangeStatus::COMPLETED => 'COMPLETED',
                    ProductQuoteChangeStatus::PROCESSING => 'PROCESSING',
                    ProductQuoteChangeStatus::IN_PROGRESS => 'IN_PROGRESS',
                ]),
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => 'Active Product quote change statuses for Involuntary flow.',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'involuntary_change_active_statuses'
        ]]);
    }
}
