<?php

use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220908_104350_add_ff_system_log_search_block_improvements_enable
 */
class m220908_104350_add_ff_system_log_search_block_improvements_enable extends Migration
{
    public function safeUp()
    {
        try {
            if (!class_exists('\kivork\FeatureFlag\Models\FeatureFlag')) {
                throw new \RuntimeException('Class (FeatureFlag) not found');
            }
            if (\kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' => \modules\featureFlag\FFlag::FF_KEY_SYSTEM_LOG_SEARCH_BLOCK_IMPROVEMENTS_ENABLE])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . \modules\featureFlag\FFlag::FF_KEY_SYSTEM_LOG_SEARCH_BLOCK_IMPROVEMENTS_ENABLE . ') already exit');
            }

            $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
            $featureFlagService::add(
                \modules\featureFlag\FFlag::FF_KEY_SYSTEM_LOG_SEARCH_BLOCK_IMPROVEMENTS_ENABLE,
                'Enable improvements in system log search block',
                \kivork\FeatureFlag\Models\FeatureFlag::TYPE_BOOL,
                false,
                \kivork\FeatureFlag\Models\FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => \modules\featureFlag\FFlag::FF_CATEGORY_SYSTEM,
                    'ff_description' => 'Enable improvements in system log search block',
                ]
            );

            \Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'm220908_104350_add_ff_system_log_search_block_improvements_enable:safeUp:Throwable'
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
            $featureFlagService::delete(\modules\featureFlag\FFlag::FF_KEY_SYSTEM_LOG_SEARCH_BLOCK_IMPROVEMENTS_ENABLE);

            \Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'm220908_104350_add_ff_system_log_search_block_improvements_enable:safeDown:Throwable'
            );
        }
    }
}
