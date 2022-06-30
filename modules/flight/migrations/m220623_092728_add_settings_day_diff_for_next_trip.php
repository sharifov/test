<?php

namespace modules\flight\migrations;

use modules\product\src\entities\productType\ProductType;
use modules\product\src\repositories\ProductTypeRepository;
use yii\db\Migration;

/**
 * Class m220623_092728_add_settings_day_diff_for_next_trip
 */
class m220623_092728_add_settings_day_diff_for_next_trip extends Migration
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
            $settings['days_diff_for_next_trip'] = 2;
            $productType->pt_settings = json_encode($settings);
            $productType->save();
            ProductTypeRepository::clearCacheById($productType->pt_id);
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
                unset($settings['days_diff_for_next_trip']);
                $productType->pt_settings = json_encode($settings);
                $productType->save();
                ProductTypeRepository::clearCacheById($productType->pt_id);
            }
        }
    }
}
