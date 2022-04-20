<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m211019_083023_add_case_category_voluntary_refund_case_category
 */
class m211019_083023_add_case_category_voluntary_refund_case_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('Cases');
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'voluntary_refund_case_category',
                's_name' => 'Voluntary Refund case category',
                's_type' => Setting::TYPE_STRING,
                's_value' => 'voluntary_refund',
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => 'Case category for Voluntary Refund processing flow',
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
            'voluntary_refund_case_category',
        ]]);
    }
}
