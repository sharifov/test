<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m211029_141136_add_default_refund_client_status_mapping
 */
class m211029_141136_add_default_refund_change_client_status_mapping extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $setting = Setting::findOne(['s_key' => 'product_quote_change_client_status_mapping']);
        if ($setting) {
            $setting->s_value = json_encode([
                "new" => "",
                "pending" => "pending",
                "confirmed" => "processing",
                "in_progress" => "processing",
                "processing" => "processing",
                "completed" => "exchanged",
                "canceled" => "",
                "declined" => "canceled",
                "error" => ""
            ]);
            $setting->save();
        }

        $setting = Setting::findOne(['s_key' => 'product_quote_refund_client_status_mapping']);
        if ($setting) {
            $setting->s_value = json_encode([
                "new" => "",
                "pending" => "pending",
                "confirmed" => "requested",
                "in_progress" => "requested",
                "processing" => "processing",
                "completed" => "refunded",
                "canceled" => "",
                "declined" => "canceled",
                "error" => ""
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
