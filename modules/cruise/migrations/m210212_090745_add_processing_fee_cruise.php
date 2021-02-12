<?php

namespace modules\cruise\migrations;

use modules\product\src\entities\productType\ProductType;
use yii\db\Migration;

/**
 * Class m210212_090745_add_processing_fee_cruise
 */
class m210212_090745_add_processing_fee_cruise extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $productCruise = \modules\product\src\entities\productType\ProductType::find()->where(['pt_id' => ProductType::PRODUCT_CRUISE])->one();

        if (!$productCruise) {
            return;
        }

        $settings = json_decode($productCruise->pt_settings, true);

        if (array_key_exists('processing_fee_amount', $settings)) {
            return;
        }

        $settings['processing_fee_amount'] = 0;

        $productCruise->pt_settings = json_encode($settings);

        $productCruise->save();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $productCruise = \modules\product\src\entities\productType\ProductType::find()->where(['pt_id' => ProductType::PRODUCT_CRUISE])->one();

        if (!$productCruise) {
            return;
        }

        $settings = json_decode($productCruise->pt_settings, true);

        if (array_key_exists('processing_fee_amount', $settings)) {
            unset($settings['processing_fee_amount']);
            $productCruise->pt_settings = json_encode($settings);
            $productCruise->save();
        }
    }
}
