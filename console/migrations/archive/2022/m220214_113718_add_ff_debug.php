<?php

use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220214_113718_add_ff_debug
 */
class m220214_113718_add_ff_debug extends Migration
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
            if (\kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' => FFlag::FF_KEY_DEBUG])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . FFlag::FF_KEY_DEBUG . ') already exit');
            }
            $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
            $featureFlagService::add(
                FFlag::FF_KEY_DEBUG,
                'Debug',
                \kivork\FeatureFlag\Models\FeatureFlag::TYPE_BOOL,
                'false',
                \kivork\FeatureFlag\Models\FeatureFlag::ET_ENABLED,
                [
                    'ff_category' => FFlag::FF_CATEGORY_SYSTEM,
                    'ff_description' => 'Debug'
                ]
            );
            Yii::$app->ff->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'm220214_113718_add_ff_debug:safeUp:Throwable');
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
            $featureFlagService::delete(FFlag::FF_KEY_DEBUG);
            Yii::$app->ff->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'm220214_113718_add_ff_debug:safeDown:Throwable');
        }
    }
}
