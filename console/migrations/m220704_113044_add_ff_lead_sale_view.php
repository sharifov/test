<?php

use yii\db\Migration;

/**
 * Class m220704_113044_add_ff_lead_sale_view
 */
class m220704_113044_add_ff_lead_sale_view extends Migration
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
            if (\kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' => \modules\featureFlag\FFlag::FF_KEY_SALE_VIEW_IN_LEAD_ENABLE])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . \modules\featureFlag\FFlag::FF_KEY_SALE_VIEW_IN_LEAD_ENABLE . ') already exist');
            }
            $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
            $featureFlagService::add(
                \modules\featureFlag\FFlag::FF_KEY_SALE_VIEW_IN_LEAD_ENABLE,
                'SaleView in Lead enable\disable',
                \kivork\FeatureFlag\Models\FeatureFlag::TYPE_BOOL,
                true,
                \kivork\FeatureFlag\Models\FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => \modules\featureFlag\FFlag::FF_CATEGORY_LEAD,
                    'ff_description' => 'SaleView view in Lead enable\disable',
                ]
            );
            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(\src\helpers\app\AppHelper::throwableLog($throwable), 'm220704_113044_add_ff_lead_sale_view:safeUp:Throwable');
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
            $featureFlagService::delete(\modules\featureFlag\FFlag::FF_KEY_SALE_VIEW_IN_LEAD_ENABLE);
            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(\src\helpers\app\AppHelper::throwableLog($throwable), 'm220704_113044_add_ff_lead_sale_view:safeDown:Throwable');
        }
    }
}
