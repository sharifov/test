<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m210303_142931_add_setting_order_auto_processing
 */
class m210303_142931_add_setting_order_auto_processing extends Migration
{
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('Order');
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'order_auto_processing_enable',
                's_name' => 'Order auto processing',
                's_type' => Setting::TYPE_BOOL,
                's_value' => false,
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
            'order_auto_processing_enable',
        ]]);
    }
}
