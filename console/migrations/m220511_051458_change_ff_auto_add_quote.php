<?php

use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220511_051458_change_ff_auto_add_quote
 */
class m220511_051458_change_ff_auto_add_quote extends Migration
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

            if (\kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' => FFlag::FF_KEY_ADD_AUTO_QUOTES])->exists()) {
                $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
                $featureFlagService::update(
                    FFlag::FF_KEY_ADD_AUTO_QUOTES,
                    ['ff_value' => 0, 'ff_description' => 'Auto add quotes in create Flight Request processing (not used as limit)']
                );
                Yii::$app->featureFlag->invalidateCache();
            }
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'm220511_051458_change_ff_auto_add_quote:safeUp:Throwable');
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
            if (\kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' => FFlag::FF_KEY_ADD_AUTO_QUOTES])->exists()) {
                $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
                $featureFlagService::update(
                    FFlag::FF_KEY_ADD_AUTO_QUOTES,
                    ['ff_value' => 5, 'ff_description' => 'Auto add quotes in create Flight Request processing']
                );
                Yii::$app->featureFlag->invalidateCache();
            }
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'm220511_051458_change_ff_auto_add_quote:safeDown:Throwable');
        }
    }
}
