<?php

use common\models\Setting;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundStatus;
use yii\db\Migration;
use yii\helpers\Json;

/**
 * Class m211027_092802_alter_refund_statuses_naming_in_site_settings
 */
class m211027_092802_alter_refund_statuses_naming_in_site_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $setting = Setting::findOne(['s_key' => 'active_quote_refund_statuses']);
        if ($setting) {
            $setting->s_value = Json::encode([
                ProductQuoteRefundStatus::PENDING => 'PENDING',
                ProductQuoteRefundStatus::CONFIRMED => 'CONFIRMED',
                ProductQuoteRefundStatus::COMPLETED => 'COMPLETED',
                ProductQuoteRefundStatus::ERROR => 'ERROR',
                ProductQuoteRefundStatus::PROCESSING => 'PROCESSING',
            ]);
            $setting->save();
        }

        $setting = Setting::findOne(['s_key' => 'finished_quote_refund_statuses']);
        if ($setting) {
            $setting->s_value = Json::encode([
                ProductQuoteRefundStatus::CANCELED => 'CANCELED',
            ]);
            $setting->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
