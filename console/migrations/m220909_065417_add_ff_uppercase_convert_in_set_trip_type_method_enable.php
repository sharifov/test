<?php

use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220909_065417_add_ff_uppercase_convert_in_set_trip_type_method_enable
 */
class m220909_065417_add_ff_uppercase_convert_in_set_trip_type_method_enable extends Migration
{
    public function safeUp()
    {
        try {
            if (!class_exists('\kivork\FeatureFlag\Models\FeatureFlag')) {
                throw new \RuntimeException('Class (FeatureFlag) not found');
            }
            if (\kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' => \modules\featureFlag\FFlag::FF_KEY_UPPERCASE_CONVERT_IN_SET_TRIP_TYPE_METHOD_ENABLE])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . \modules\featureFlag\FFlag::FF_KEY_UPPERCASE_CONVERT_IN_SET_TRIP_TYPE_METHOD_ENABLE . ') already exit');
            }

            $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
            $featureFlagService::add(
                \modules\featureFlag\FFlag::FF_KEY_UPPERCASE_CONVERT_IN_SET_TRIP_TYPE_METHOD_ENABLE,
                'Enable converting trip_type key in setTripType method',
                \kivork\FeatureFlag\Models\FeatureFlag::TYPE_BOOL,
                false,
                \kivork\FeatureFlag\Models\FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => \modules\featureFlag\FFlag::FF_CATEGORY_SYSTEM,
                    'ff_description' => 'Enable converting trip_type key in setTripType method',
                ]
            );

            \Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'm220909_065417_add_ff_uppercase_convert_in_set_trip_type_method_enable:safeUp:Throwable'
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
            $featureFlagService::delete(\modules\featureFlag\FFlag::FF_KEY_UPPERCASE_CONVERT_IN_SET_TRIP_TYPE_METHOD_ENABLE);

            \Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'm220909_065417_add_ff_uppercase_convert_in_set_trip_type_method_enable:safeDown:Throwable'
            );
        }
    }
}
