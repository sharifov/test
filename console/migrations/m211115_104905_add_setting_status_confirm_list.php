<?php

use common\models\Setting;
use common\models\SettingCategory;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use yii\db\Migration;

/**
 * Class m211115_104905_add_setting_status_confirm_list
 */
class m211115_104905_add_setting_status_confirm_list extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('Product Quote');
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'exchange_quote_confirm_status_list',
                's_name' => 'Exchange Quote Confirm Status List',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => json_encode([
                    ProductQuoteStatus::NEW => 'NEW',
                    ProductQuoteStatus::PENDING => 'PENDING',
                    ProductQuoteStatus::IN_PROGRESS => 'IN_PROGRESS',
                    ProductQuoteStatus::APPLIED => 'APPLIED',
                    ProductQuoteStatus::ERROR => 'ERROR',
                ]),
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => '',
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
            'exchange_quote_confirm_status_list',
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
