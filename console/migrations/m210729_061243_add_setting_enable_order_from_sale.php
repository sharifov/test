<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m210729_061243_add_setting_enable_order_from_sale
 */
class m210729_061243_add_setting_enable_order_from_sale extends Migration
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
                's_key' => 'enable_order_from_sale',
                's_name' => 'Enable Import Order from Sale',
                's_type' => Setting::TYPE_BOOL,
                's_value' => false,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => 'this setting enables/disables import of sales into orders, on the case/view page when adding sales, as well as in the console script - sync/sales',
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
            'enable_order_from_sale',
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
