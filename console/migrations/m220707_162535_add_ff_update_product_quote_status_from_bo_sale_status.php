<?php

use yii\db\Migration;

/**
 * Class m220707_162535_add_ff_update_product_quote_status_from_bo_sale_status
 */
class m220707_162535_add_ff_update_product_quote_status_from_bo_sale_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            if (!class_exists('\kivork\FeatureFlag\Models\FeatureFlag')) {
                throw new \RuntimeException('Class (FeatureFlag) not found');
            }
            if (\kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' => \modules\featureFlag\FFlag::FF_KEY_UPDATE_PRODUCT_QUOTE_STATUS_BY_BO_SALE_STATUS])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . \modules\featureFlag\FFlag::FF_KEY_UPDATE_PRODUCT_QUOTE_STATUS_BY_BO_SALE_STATUS . ') already exist');
            }
            $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
            $featureFlagService::add(
                \modules\featureFlag\FFlag::FF_KEY_UPDATE_PRODUCT_QUOTE_STATUS_BY_BO_SALE_STATUS,
                'Update product quote status by BO sale status',
                \kivork\FeatureFlag\Models\FeatureFlag::TYPE_BOOL,
                true,
                \kivork\FeatureFlag\Models\FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => \modules\featureFlag\FFlag::FF_CATEGORY_SYSTEM,
                    'ff_description' => 'Compare original product quote status with sale status from BO, if differ then update original product quote status.',
                ]
            );
            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(\src\helpers\app\AppHelper::throwableLog($throwable), 'm220707_162535_add_ff_update_product_quote_status_from_bo_sale_status:safeUp:Throwable');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            if (!class_exists('\kivork\FeatureFlag\Models\FeatureFlag')) {
                throw new \RuntimeException('Class (FeatureFlag) not found');
            }
            $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
            $featureFlagService::delete(\modules\featureFlag\FFlag::FF_KEY_UPDATE_PRODUCT_QUOTE_STATUS_BY_BO_SALE_STATUS);
            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(\src\helpers\app\AppHelper::throwableLog($throwable), 'm220707_162535_add_ff_update_product_quote_status_from_bo_sale_status:safeDown:Throwable');
        }
    }
}
