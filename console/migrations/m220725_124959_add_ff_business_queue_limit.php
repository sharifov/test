<?php

use kivork\FeatureFlag\Models\FeatureFlag;
use kivork\FeatureFlag\Services\FeatureFlagService;
use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220725_124959_add_ff_business_queue_limit
 */
class m220725_124959_add_ff_business_queue_limit extends Migration
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

            if (FeatureFlag::find()->where(['ff_key' =>  FFlag::FF_KEY_BUSINESS_QUEUE_LIMIT])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . FFlag::FF_KEY_BUSINESS_QUEUE_LIMIT . ') already exist');
            }

            $featureFlagService = new FeatureFlagService();
            $featureFlagService::add(
                FFlag::FF_KEY_BUSINESS_QUEUE_LIMIT,
                'Business Queue Limit Enable',
                FeatureFlag::TYPE_BOOL,
                true,
                FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => FFlag::FF_KEY_BUSINESS_QUEUE_LIMIT,
                    'ff_description' => 'Business Queue Limit Enable',
                ]
            );

            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220725_124959_add_ff_business_queue_limit:safeUp:Throwable');
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
            $featureFlagService::delete(FFlag::FF_KEY_BUSINESS_QUEUE_LIMIT);

            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220725_124959_add_ff_business_queue_limit:safeDown:Throwable');
        }
    }
}
