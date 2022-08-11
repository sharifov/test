<?php

use kivork\FeatureFlag\Models\FeatureFlag;
use kivork\FeatureFlag\Services\FeatureFlagService;
use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220715_053957_add_ff_return_flight_segment_autocomplete_enable
 */
class m220715_053957_add_ff_return_flight_segment_autocomplete_enable extends Migration
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

            if (FeatureFlag::find()->where(['ff_key' =>  FFlag::FF_KEY_RETURN_FLIGHT_SEGMENT_AUTOCOMPLETE_ENABLE])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . FFlag::FF_KEY_RETURN_FLIGHT_SEGMENT_AUTOCOMPLETE_ENABLE . ') already exist');
            }

            $featureFlagService = new FeatureFlagService();
            $featureFlagService::add(
                FFlag::FF_KEY_RETURN_FLIGHT_SEGMENT_AUTOCOMPLETE_ENABLE,
                'Return flight segment autocomplete enable',
                FeatureFlag::TYPE_BOOL,
                true,
                FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => FFlag::FF_KEY_RETURN_FLIGHT_SEGMENT_AUTOCOMPLETE_ENABLE,
                    'ff_description' => 'Return flight segment autocomplete enable',
                ]
            );

            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220715_053957_add_ff_return_flight_segment_autocomplete_enable:safeUp:Throwable');
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
            $featureFlagService::delete(FFlag::FF_KEY_RETURN_FLIGHT_SEGMENT_AUTOCOMPLETE_ENABLE);

            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220715_053957_add_ff_return_flight_segment_autocomplete_enable:safeDown:Throwable');
        }
    }
}
