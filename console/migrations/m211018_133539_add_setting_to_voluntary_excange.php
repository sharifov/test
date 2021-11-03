<?php

use common\models\Setting;
use common\models\SettingCategory;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundStatus;
use yii\db\Migration;

/**
 * Class m211018_133539_add_setting_to_voluntary_excange
 */
class m211018_133539_add_setting_to_voluntary_excange extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'voluntary_exchange_processing_status_list'
        ]]);

        $settingCategory = SettingCategory::getOrCreateByName('Product Quote');
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'product_quote_changeable_statuses',
                's_name' => 'Product quote changeable statuses',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => json_encode([
                    ProductQuoteStatus::BOOKED => 'BOOKED',
                    ProductQuoteStatus::SOLD => 'SOLD',
                ]),
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => 'Product quote changeable statuses for Voluntary Exchange and Refund processing flow.',
            ]
        );

        $settingCategory = SettingCategory::getOrCreateByName('Product Quote Change');
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'active_quote_change_statuses',
                's_name' => 'Active Product quote change statuses',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => json_encode([
                    ProductQuoteChangeStatus::PENDING => 'PENDING',
                    ProductQuoteChangeStatus::IN_PROGRESS => 'IN_PROGRESS',
                    ProductQuoteChangeStatus::ERROR => 'ERROR',
                    ProductQuoteChangeStatus::PROCESSING => 'PROCESSING',
                ]),
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => 'Active Product quote change statuses for Voluntary Exchange and Refund processing flow.',
            ]
        );

        $settingCategory = SettingCategory::getOrCreateByName('Product Quote Change');
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'active_quote_refund_statuses',
                's_name' => 'Active Product quote refund statuses',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => json_encode([
                    ProductQuoteRefundStatus::PENDING => 'PENDING',
                    ProductQuoteRefundStatus::CONFIRMED => 'CONFIRMED',
                    ProductQuoteRefundStatus::COMPLETED => 'COMPLETED',
                    ProductQuoteRefundStatus::ERROR => 'ERROR',
                    ProductQuoteRefundStatus::PROCESSING => 'PROCESSING',
                ]),
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => 'Active Product quote change statuses for Voluntary Exchange and Refund processing flow.',
            ]
        );

        $settingCategory = SettingCategory::getOrCreateByName('Product Quote Change');
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'finished_quote_change_statuses',
                's_name' => 'Finished Product quote change statuses',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => json_encode([
                    ProductQuoteChangeStatus::CANCELED => 'CANCELED',
                    ProductQuoteChangeStatus::DECLINED => 'DECLINED',
                ]),
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => 'Finished Product Quote change statuses for Voluntary Exchange processing flow.',
            ]
        );

        $settingCategory = SettingCategory::getOrCreateByName('Product Quote Change');
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'finished_quote_refund_statuses',
                's_name' => 'Finished Product quote refund statuses',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => json_encode([
                    ProductQuoteRefundStatus::CANCELED => 'CANCELED',
                ]),
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => 'Finished Product Quote change statuses for Refund processing flow.',
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
            'product_quote_changeable_statuses',
            'active_quote_change_statuses',
            'active_quote_refund_statuses',
            'finished_quote_change_statuses',
            'finished_quote_refund_statuses',
        ]]);

        $settingCategory = SettingCategory::getOrCreateByName('Product Quote Change');
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'voluntary_exchange_processing_status_list',
                's_name' => 'Voluntary Exchange Processing Status List',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => json_encode([
                    ProductQuoteChangeStatus::PENDING => 'PENDING',
                    ProductQuoteChangeStatus::IN_PROGRESS => 'IN_PROGRESS',
                    ProductQuoteChangeStatus::ERROR => 'ERROR',
                    ProductQuoteChangeStatus::PROCESSING => 'PROCESSING',
                ]),
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => 'ProductQuoteChange Processing Status List for Voluntary Exchange processing flow.',
            ]
        );

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
