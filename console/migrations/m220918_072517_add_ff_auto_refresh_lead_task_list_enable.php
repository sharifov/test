<?php

use yii\db\Migration;
use src\helpers\app\AppHelper;

/**
 * Class m220918_072517_add_ff_auto_refresh_lead_task_list_enable
 */
class m220918_072517_add_ff_auto_refresh_lead_task_list_enable extends Migration
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
            if (\kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' => \modules\featureFlag\FFlag::FF_KEY_AUTO_REFRESH_LEAD_TASK_LIST_ENABLE])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . \modules\featureFlag\FFlag::FF_KEY_AUTO_REFRESH_LEAD_TASK_LIST_ENABLE . ') already exit');
            }

            $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
            $featureFlagService::add(
                \modules\featureFlag\FFlag::FF_KEY_AUTO_REFRESH_LEAD_TASK_LIST_ENABLE,
                'Auto refresh lead task list enabled',
                \kivork\FeatureFlag\Models\FeatureFlag::TYPE_BOOL,
                false,
                \kivork\FeatureFlag\Models\FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => \modules\featureFlag\FFlag::FF_CATEGORY_LEAD,
                    'ff_description' => 'Auto refresh lead task list enabled',
                ]
            );

            \Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'm220918_072517_add_ff_auto_refresh_lead_task_list_enable:safeUp:Throwable'
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
            $featureFlagService::delete(\modules\featureFlag\FFlag::FF_KEY_AUTO_REFRESH_LEAD_TASK_LIST_ENABLE);

            \Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'm220918_072517_add_ff_auto_refresh_lead_task_list_enable:safeDown:Throwable'
            );
        }
    }
}
