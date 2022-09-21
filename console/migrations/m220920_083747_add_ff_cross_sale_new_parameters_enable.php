<?php

use yii\db\Migration;

/**
 * Class m220920_083747_add_ff_cross_sale_new_parameters_enable
 */
class m220920_083747_add_ff_cross_sale_new_parameters_enable extends Migration
{
    public function safeUp()
    {
        try {
            if (!class_exists('\kivork\FeatureFlag\Models\FeatureFlag')) {
                throw new \RuntimeException('Class (FeatureFlag) not found');
            }
            if (\kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' => \modules\featureFlag\FFlag::FF_KEY_CROSS_SALE_NEW_PARAMETERS_ENABLE])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . \modules\featureFlag\FFlag::FF_KEY_CROSS_SALE_NEW_PARAMETERS_ENABLE . ') already exit');
            }

            $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
            $featureFlagService::add(
                \modules\featureFlag\FFlag::FF_KEY_CROSS_SALE_NEW_PARAMETERS_ENABLE,
                'New parameters for cross sale enable',
                \kivork\FeatureFlag\Models\FeatureFlag::TYPE_BOOL,
                false,
                \kivork\FeatureFlag\Models\FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => \modules\featureFlag\FFlag::FF_CATEGORY_LEAD,
                    'ff_description' => 'New parameters for cross sale enable',
                ]
            );

            \Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                src\helpers\app\AppHelper::throwableLog($throwable),
                'm220920_083747_add_ff_cross_sale_new_parameters_enable:safeUp:Throwable'
            );
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
            $featureFlagService::delete(\modules\featureFlag\FFlag::FF_KEY_CROSS_SALE_NEW_PARAMETERS_ENABLE);

            \Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                src\helpers\app\AppHelper::throwableLog($throwable),
                'm220920_083747_add_ff_cross_sale_new_parameters_enable:safeDown:Throwable'
            );
        }
    }
}
