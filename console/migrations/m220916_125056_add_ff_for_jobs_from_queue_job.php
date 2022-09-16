<?php

use modules\featureFlag\FFlag;
use yii\db\Migration;

/**
 * Class m220916_125056_add_ff_for_jobs_from_queue_job
 */
class m220916_125056_add_ff_for_jobs_from_queue_job extends Migration
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

            if (!\kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' => FFlag::FF_KEY_LOGGING_EXECUTION_TIME_FOR_JOBS_FROM_QUEUE_JOB])->exists()) {
                $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
                $featureFlagService::add(
                    FFlag::FF_KEY_LOGGING_EXECUTION_TIME_FOR_JOBS_FROM_QUEUE_JOB,
                    'Logging execution time for jobs of queue_job',
                    \kivork\FeatureFlag\Models\FeatureFlag::TYPE_BOOL,
                    true,
                    \kivork\FeatureFlag\Models\FeatureFlag::ET_DISABLED,
                    [
                        'ff_category' => FFlag::FF_CATEGORY_SYSTEM,
                        'ff_description' => 'Enabling logging execution time for jobs of queue_job'
                    ]
                );
                Yii::$app->featureFlag->invalidateCache();
            }
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'm220916_125056_add_ff_for_jobs_from_queue_job:safeUp:Throwable');
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
            $featureFlagService::delete(FFlag::FF_KEY_LOGGING_EXECUTION_TIME_FOR_JOBS_FROM_QUEUE_JOB);
            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'm220916_125056_add_ff_for_jobs_from_queue_job:safeDown:Throwable');
        }
    }
}
