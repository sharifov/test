<?php

use yii\db\Migration;

/**
 * Class m220727_120310_add_ff_schedule_change_client_notification_remainder
 */
class m220727_120310_add_ff_schedule_change_client_notification_remainder extends Migration
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
            if (\kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' => \modules\featureFlag\FFlag::FF_KEY_SCHEDULE_CHANGE_CLIENT_REMAINDER_NOTIFICATION])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . \modules\featureFlag\FFlag::FF_KEY_SCHEDULE_CHANGE_CLIENT_REMAINDER_NOTIFICATION . ') already exist');
            }
            $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
            $featureFlagService::add(
                \modules\featureFlag\FFlag::FF_KEY_SCHEDULE_CHANGE_CLIENT_REMAINDER_NOTIFICATION,
                'Create remainder notification for schedule change',
                \kivork\FeatureFlag\Models\FeatureFlag::TYPE_BOOL,
                true,
                \kivork\FeatureFlag\Models\FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => \modules\featureFlag\FFlag::FF_CATEGORY_SYSTEM,
                    'ff_description' => 'When product flight quote is added, create remainder notification. (email, sms, phone)',
                ]
            );
            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(\src\helpers\app\AppHelper::throwableLog($throwable), 'm220727_120310_add_ff_schedule_change_client_notification_remainder:safeUp:Throwable');
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
            $featureFlagService::delete(\modules\featureFlag\FFlag::FF_KEY_SCHEDULE_CHANGE_CLIENT_REMAINDER_NOTIFICATION);
            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(\src\helpers\app\AppHelper::throwableLog($throwable), 'm220727_120310_add_ff_schedule_change_client_notification_remainder:safeDown:Throwable');
        }
    }
}
