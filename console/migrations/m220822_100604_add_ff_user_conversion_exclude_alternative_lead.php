<?php

use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220822_100604_add_ff_user_conversion_exclude_alternative_lead
 */
class m220822_100604_add_ff_user_conversion_exclude_alternative_lead extends Migration
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
            if (\kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' => \modules\featureFlag\FFlag::FF_KEY_USER_CONVERSION_EXCLUDE_ALTERNATIVE_LEAD_ENABLE])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . \modules\featureFlag\FFlag::FF_KEY_USER_CONVERSION_EXCLUDE_ALTERNATIVE_LEAD_ENABLE . ') already exit');
            }

            $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
            $featureFlagService::add(
                \modules\featureFlag\FFlag::FF_KEY_USER_CONVERSION_EXCLUDE_ALTERNATIVE_LEAD_ENABLE,
                'Exclude alternative Lead in user conversion',
                \kivork\FeatureFlag\Models\FeatureFlag::TYPE_BOOL,
                false,
                \kivork\FeatureFlag\Models\FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => \modules\featureFlag\FFlag::FF_CATEGORY_LEAD,
                    'ff_description' => 'Exclude alternative Lead in user conversion'
                ]
            );

            \Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'm220822_100604_add_ff_user_conversion_exclude_alternative_lead:safeUp:Throwable'
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
            $featureFlagService::delete(\modules\featureFlag\FFlag::FF_KEY_USER_CONVERSION_EXCLUDE_ALTERNATIVE_LEAD_ENABLE);

            \Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'm220822_100604_add_ff_user_conversion_exclude_alternative_lead:safeDown:Throwable'
            );
        }
    }
}
