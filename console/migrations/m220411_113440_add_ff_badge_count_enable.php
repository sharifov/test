<?php

use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220411_113440_add_ff_badge_count_enable
 */
class m220411_113440_add_ff_badge_count_enable extends Migration
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
            if (\kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' => FFlag::FF_KEY_BADGE_COUNT_ENABLE])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . FFlag::FF_KEY_BADGE_COUNT_ENABLE . ') already exit');
            }
            $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
            $featureFlagService::add(
                FFlag::FF_KEY_BADGE_COUNT_ENABLE,
                'Badge Count Enable',
                \kivork\FeatureFlag\Models\FeatureFlag::TYPE_BOOL,
                'true',
                \kivork\FeatureFlag\Models\FeatureFlag::ET_ENABLED,
                [
                    'ff_category' => FFlag::FF_CATEGORY_SYSTEM,
                    'ff_description' => 'Badge Count Enable/Disable'
                ]
            );
            Yii::$app->ff->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'm220411_113440_add_ff_badge_count_enable:safeUp:Throwable');
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
            $featureFlagService::delete(FFlag::FF_KEY_BADGE_COUNT_ENABLE);
            Yii::$app->ff->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'm220411_113440_add_ff_badge_count_enable:safeDown:Throwable');
        }
    }
}
