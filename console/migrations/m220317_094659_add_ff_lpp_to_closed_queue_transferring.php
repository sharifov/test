<?php

use yii\db\Migration;

/**
 * Class m220317_094659_add_ff_lpp_to_closed_queue_transferring
 */
class m220317_094659_add_ff_lpp_to_closed_queue_transferring extends Migration
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
            if (\kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' =>  \modules\featureFlag\FFlag::FF_KEY_LPP_TO_CLOSED_QUEUE_TRANSFERRING_DAYS_COUNT])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . \modules\featureFlag\FFlag::FF_KEY_LPP_TO_CLOSED_QUEUE_TRANSFERRING_DAYS_COUNT . ') already exist');
            }
            $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
            $featureFlagService::add(
                \modules\featureFlag\FFlag::FF_KEY_LPP_TO_CLOSED_QUEUE_TRANSFERRING_DAYS_COUNT,
                'Lead Poor Processing transferring From Extra to Closed days count',
                \kivork\FeatureFlag\Models\FeatureFlag::TYPE_INT,
                10,
                \kivork\FeatureFlag\Models\FeatureFlag::ET_ENABLED,
                [
                    'ff_category' => \modules\featureFlag\FFlag::FF_CATEGORY_LEAD,
                    'ff_description' => 'Lead Poor Processing transferring From Extra to Closed days count'
                ]
            );
            Yii::$app->ff->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(\src\helpers\app\AppHelper::throwableLog($throwable), 'm220317_094659_add_ff_lpp_to_closed_queue_transferring:safeUp:Throwable');
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
            $featureFlagService::delete(\modules\featureFlag\FFlag::FF_KEY_LPP_TO_CLOSED_QUEUE_TRANSFERRING_DAYS_COUNT);
            Yii::$app->ff->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(\src\helpers\app\AppHelper::throwableLog($throwable), 'm220317_094659_add_ff_lpp_to_closed_queue_transferring:safeDown:Throwable');
        }
    }
}
