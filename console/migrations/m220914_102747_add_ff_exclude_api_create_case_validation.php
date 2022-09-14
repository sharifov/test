<?php

use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220914_102747_add_ff_exclude_api_create_case_validation
 */
class m220914_102747_add_ff_exclude_api_create_case_validation extends Migration
{
    public function safeUp()
    {
        try {
            if (!class_exists('\kivork\FeatureFlag\Models\FeatureFlag')) {
                throw new \RuntimeException('Class (FeatureFlag) not found');
            }
            if (\kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' => \modules\featureFlag\FFlag::FF_KEY_EXCLUDE_API_CREATE_CASE_VALIDATION])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . \modules\featureFlag\FFlag::FF_KEY_EXCLUDE_API_CREATE_CASE_VALIDATION . ') already exit');
            }

            $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
            $featureFlagService::add(
                \modules\featureFlag\FFlag::FF_KEY_EXCLUDE_API_CREATE_CASE_VALIDATION,
                'Exclude create api create case validation',
                \kivork\FeatureFlag\Models\FeatureFlag::TYPE_BOOL,
                false,
                \kivork\FeatureFlag\Models\FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => \modules\featureFlag\FFlag::FF_CATEGORY_SYSTEM,
                    'ff_description' => 'Exclude create api create case validation (email, phone, chat visitor, booking Id)',
                ]
            );

            \Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'm220914_102747_add_ff_exclude_api_create_case_validation:safeUp:Throwable'
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
            $featureFlagService::delete(\modules\featureFlag\FFlag::FF_KEY_EXCLUDE_API_CREATE_CASE_VALIDATION);

            \Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'm220914_102747_add_ff_exclude_api_create_case_validation:safeDown:Throwable'
            );
        }
    }
}
