<?php

namespace modules\product\migrations;

use modules\product\src\entities\productType\ProductType;
use yii\db\Migration;

/**
 * Class m220517_121032_add_settings_expiration_date_quote
 */
class m220517_121032_add_settings_expiration_date_quote extends Migration
{
    public const KEY = 'flight';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $productType = ProductType::findOne(['pt_key' => self::KEY]);
        if ($productType) {
            $settings = json_decode($productType->pt_settings, true) ?: [];
            $settings['expiration_days_of_new_offers'] = 7;
            $settings['minimum_hours_difference_between_offers'] = 24;
            $productType->pt_settings = json_encode($settings);
            $productType->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $productType = ProductType::findOne(['pt_key' => self::KEY]);
        if ($productType) {
            $settings = json_decode($productType->pt_settings, true) ?: [];
            if ($settings) {
                unset($settings['expiration_days_of_new_offers'], $settings['minimum_hours_difference_between_offers']);
                $productType->pt_settings = json_encode($settings);
                $productType->save();
            }
        }
    }
}
