<?php

use yii\db\Migration;
use src\helpers\app\AppHelper;

/**
 * Class m220914_054143_add_ff_user_new_task_list_cache_duration
 */
class m220914_054143_add_ff_user_new_task_list_cache_duration extends Migration
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
            if (\kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' => \modules\featureFlag\FFlag::FF_KEY_USER_NEW_TASK_LIST_CACHE_DURATION])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . \modules\featureFlag\FFlag::FF_KEY_USER_NEW_TASK_LIST_CACHE_DURATION . ') already exit');
            }

            $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
            $featureFlagService::add(
                \modules\featureFlag\FFlag::FF_KEY_USER_NEW_TASK_LIST_CACHE_DURATION,
                'Cache duration for new user task list. (in seconds)',
                \kivork\FeatureFlag\Models\FeatureFlag::TYPE_INT,
                0,
                \kivork\FeatureFlag\Models\FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => \modules\featureFlag\FFlag::FF_CATEGORY_LEAD,
                    'ff_description' => 'Cache duration for new user task list. (in seconds)',
                ]
            );

            \Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'm220914_054143_add_ff_user_new_task_list_cache_duration:safeUp:Throwable'
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
            $featureFlagService::delete(\modules\featureFlag\FFlag::FF_KEY_USER_NEW_TASK_LIST_CACHE_DURATION);

            \Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'm220914_054143_add_ff_user_new_task_list_cache_duration:safeDown:Throwable'
            );
        }
    }
}
