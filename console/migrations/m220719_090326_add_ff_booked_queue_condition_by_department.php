<?php

use kivork\FeatureFlag\Models\FeatureFlag;
use kivork\FeatureFlag\Services\FeatureFlagService;
use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220719_090326_add_ff_booked_queue_condition_by_department
 */
class m220719_090326_add_ff_booked_queue_condition_by_department extends Migration
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

            if (FeatureFlag::find()->where(['ff_key' =>  FFlag::FF_KEY_BOOKED_QUEUE_CONDITION_BY_DEPARTMENT])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . FFlag::FF_KEY_BOOKED_QUEUE_CONDITION_BY_DEPARTMENT . ') already exist');
            }

            $featureFlagService = new FeatureFlagService();
            $featureFlagService::add(
                FFlag::FF_KEY_BOOKED_QUEUE_CONDITION_BY_DEPARTMENT,
                'Booked Queue condition by department enable',
                FeatureFlag::TYPE_BOOL,
                true,
                FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => FFlag::FF_KEY_BOOKED_QUEUE_CONDITION_BY_DEPARTMENT,
                    'ff_description' => 'Booked Queue condition by department enable',
                ]
            );

            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220719_090326_add_ff_booked_queue_condition_by_department:safeUp:Throwable');
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
            $featureFlagService::delete(FFlag::FF_KEY_BOOKED_QUEUE_CONDITION_BY_DEPARTMENT);

            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220719_090326_add_ff_booked_queue_condition_by_department:safeDown:Throwable');
        }
    }
}
