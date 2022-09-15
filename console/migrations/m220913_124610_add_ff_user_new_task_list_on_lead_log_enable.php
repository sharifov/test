<?php

use yii\db\Migration;
use src\helpers\app\AppHelper;

/**
 * Class m220913_124610_add_ff_user_new_task_list_on_lead_log_enable
 */
class m220913_124610_add_ff_user_new_task_list_on_lead_log_enable extends Migration
{
    public function safeUp()
    {
        try {
            if (!class_exists('\kivork\FeatureFlag\Models\FeatureFlag')) {
                throw new \RuntimeException('Class (FeatureFlag) not found');
            }
            if (\kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' => \modules\featureFlag\FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . \modules\featureFlag\FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE . ') already exit');
            }

            $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
            $featureFlagService::add(
                \modules\featureFlag\FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE,
                'Log new task list on lead page',
                \kivork\FeatureFlag\Models\FeatureFlag::TYPE_BOOL,
                false,
                \kivork\FeatureFlag\Models\FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => \modules\featureFlag\FFlag::FF_CATEGORY_LEAD,
                    'ff_description' => 'Log User Task List on lead page',
                ]
            );

            \Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'm220913_124610_add_ff_user_new_task_list_on_lead_log_enable:safeUp:Throwable'
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
            $featureFlagService::delete(\modules\featureFlag\FFlag::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE);

            \Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'm220913_124610_add_ff_user_new_task_list_on_lead_log_enable:safeDown:Throwable'
            );
        }
    }
}
