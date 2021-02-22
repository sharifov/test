<?php

namespace modules\rentCar\migrations;

use modules\product\src\entities\productType\ProductType;
use yii\db\Migration;

/**
 * Class m210212_091341_add_processing_fee_to_rent_car
 */
class m210212_091341_add_processing_fee_to_rent_car extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($productType = ProductType::findOne(['pt_key' => 'rent_car'])) {
            $settings = json_decode($productType->pt_settings, true);
            $settings['processing_fee_amount'] = 0.00;
            $productType->pt_settings = json_encode($settings);
            $productType->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210212_091341_add_processing_fee_to_rent_car cannot be reverted.\n";

        return false;
    }
}
