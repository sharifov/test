<?php

use yii\db\Migration;
use kivork\FeatureFlag\Models\FeatureFlag;
use kivork\FeatureFlag\Services\FeatureFlagService;
use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;

/**
 * Class m220722_120920_add_ff_for_beq_to_closed_days_count
 */
class m220722_120920_add_ff_for_beq_to_closed_days_count extends Migration
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

            if (FeatureFlag::find()->where(['ff_key' =>  FFlag::FF_KEY_BEQ_TO_CLOSED_QUEUE_TRANSFERRING_DAYS_COUNT])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . FFlag::FF_KEY_BEQ_TO_CLOSED_QUEUE_TRANSFERRING_DAYS_COUNT . ') already exist');
            }

            $featureFlagService = new FeatureFlagService();
            $featureFlagService::add(
                FFlag::FF_KEY_BEQ_TO_CLOSED_QUEUE_TRANSFERRING_DAYS_COUNT,
                'Lead Business Extra Queue transferring From Extra to Closed days count',
                \kivork\FeatureFlag\Models\FeatureFlag::TYPE_INT,
                10,
                FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => FFlag::FF_CATEGORY_SYSTEM,
                    'ff_description' => 'Lead Business Extra Queue transferring From Extra to Closed days count',
                ]
            );

            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220722_120920_add_ff_for_beq_to_closed_days_count:safeUp:Throwable');
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
            $featureFlagService::delete(FFlag::FF_KEY_BEQ_TO_CLOSED_QUEUE_TRANSFERRING_DAYS_COUNT);

            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220722_120920_add_ff_for_beq_to_closed_days_count:safeDown:Throwable');
        }
    }
}
