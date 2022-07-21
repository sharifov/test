<?php

use kivork\FeatureFlag\Models\FeatureFlag;
use kivork\FeatureFlag\Services\FeatureFlagService;
use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220720_135849_add_ff_shift_schedule_request_save_send_notification_by_job
 */
class m220720_135849_add_ff_shift_schedule_request_save_send_notification_by_job extends Migration
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

            if (FeatureFlag::find()->where(['ff_key' =>  FFlag::FF_KEY_SHIFT_SCHEDULE_REQUEST_SAVE_SEND_NOTIFICATION_BY_JOB_ENABLE])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . FFlag::FF_KEY_SHIFT_SCHEDULE_REQUEST_SAVE_SEND_NOTIFICATION_BY_JOB_ENABLE . ') already exist');
            }

            $featureFlagService = new FeatureFlagService();
            $featureFlagService::add(
                FFlag::FF_KEY_SHIFT_SCHEDULE_REQUEST_SAVE_SEND_NOTIFICATION_BY_JOB_ENABLE,
                'Enable send notification from job, when request was saved',
                FeatureFlag::TYPE_BOOL,
                true,
                FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => FFlag::FF_CATEGORY_SHIFT_SCHEDULE,
                    'ff_description' => 'Enable send notification from job, when request was saved',
                ]
            );

            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220720_135849_add_ff_shift_schedule_request_save_send_notification_by_job:safeUp:Throwable');
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
            $featureFlagService::delete(FFlag::FF_KEY_SHIFT_SCHEDULE_REQUEST_SAVE_SEND_NOTIFICATION_BY_JOB_ENABLE);

            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220720_135849_add_ff_shift_schedule_request_save_send_notification_by_job:safeDown:Throwable');
        }
    }
}
