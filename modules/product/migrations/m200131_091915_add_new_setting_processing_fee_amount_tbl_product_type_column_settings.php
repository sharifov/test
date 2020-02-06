<?php
namespace modules\product\migrations;

use yii\db\Migration;

/**
 * Class m200131_091915_add_new_setting_processing_fee_amount_tbl_product_type_column_settings
 */
class m200131_091915_add_new_setting_processing_fee_amount_tbl_product_type_column_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$productFlight = \modules\product\src\entities\productType\ProductType::find()->where(['like', 'pt_name', '%flight%', false])->one();

		$settings = json_decode($productFlight->pt_settings, true);

		$settings['processing_fee_amount'] = 25.00;

		$productFlight->pt_settings = json_encode($settings);

		$productFlight->save();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$productFlight = \modules\product\src\entities\productType\ProductType::find()->where(['like', 'pt_name', '%flight%', false])->one();

		$settings = json_decode($productFlight->pt_settings, true);

		if (isset($settings['processing_fee_amount'])) {
			unset($settings['processing_fee_amount']);
			$productFlight->pt_settings = json_encode($settings);
			$productFlight->save();
		}
    }
}
