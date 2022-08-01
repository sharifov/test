<?php

use kivork\FeatureFlag\Models\FeatureFlag;
use kivork\FeatureFlag\Services\FeatureFlagService;
use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220729_120526_add_ff_refactoring_monitor_incoming_call
 */
class m220729_120526_add_ff_refactoring_monitor_incoming_call extends Migration
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
            if (FeatureFlag::find()->where(['ff_key' =>  FFlag::FF_KEY_REFACTORING_INCOMING_CALL_ENABLE])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . FFlag::FF_KEY_REFACTORING_INCOMING_CALL_ENABLE . ') already exist');
            }

            $featureFlagService = new FeatureFlagService();
            $featureFlagService::add(
                FFlag::FF_KEY_REFACTORING_INCOMING_CALL_ENABLE,
                'Switch incoming monitor page to new version',
                FeatureFlag::TYPE_BOOL,
                true,
                FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => FFlag::FF_CATEGORY_SYSTEM,
                    'ff_description' => 'Switch incoming monitor page to new version',
                ]
            );

            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220729_120526_add_ff_refactoring_monitor_incoming_call:safeUp:Throwable');
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

            $featureFlagService = new FeatureFlagService();
            $featureFlagService::delete(FFlag::FF_KEY_REFACTORING_INCOMING_CALL_ENABLE);

            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220729_120526_add_ff_refactoring_monitor_incoming_call:safeDown:Throwable');
        }
    }
}
