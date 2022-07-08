<?php

use yii\db\Migration;

/**
 * Class m220708_141510_add_ff_send_additional_info_to_bo_endpoints
 */
class m220708_141510_add_ff_send_additional_info_to_bo_endpoints extends Migration
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
            if (\kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' => \modules\featureFlag\FFlag::FF_KEY_SEND_ADDITIONAL_INFO_TO_BO_ENDPOINTS])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . \modules\featureFlag\FFlag::FF_KEY_SEND_ADDITIONAL_INFO_TO_BO_ENDPOINTS . ') already exist');
            }
            $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
            $featureFlagService::add(
                \modules\featureFlag\FFlag::FF_KEY_SEND_ADDITIONAL_INFO_TO_BO_ENDPOINTS,
                'Send additional info to BO endpoints',
                \kivork\FeatureFlag\Models\FeatureFlag::TYPE_BOOL,
                true,
                \kivork\FeatureFlag\Models\FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => \modules\featureFlag\FFlag::FF_CATEGORY_SYSTEM,
                    'ff_description' => 'Send additional info to BO endpoints (who created quote and quote created date)',
                ]
            );
            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(\src\helpers\app\AppHelper::throwableLog($throwable), 'm220708_141510_add_ff_send_additional_info_to_bo_endpoints:safeUp:Throwable');
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
            $featureFlagService::delete(\modules\featureFlag\FFlag::FF_KEY_SEND_ADDITIONAL_INFO_TO_BO_ENDPOINTS);
            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(\src\helpers\app\AppHelper::throwableLog($throwable), 'm220708_141510_add_ff_send_additional_info_to_bo_endpoints:safeDown:Throwable');
        }
    }
}
