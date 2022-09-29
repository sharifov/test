<?php

use yii\db\Migration;

/**
 * Class m220927_054714_add_ff_attach_lead_to_hotel_order
 */
class m220927_054714_add_ff_attach_lead_to_hotel_order extends Migration
{
    public function safeUp()
    {
        try {
            if (!class_exists('\kivork\FeatureFlag\Models\FeatureFlag')) {
                throw new \RuntimeException('Class (FeatureFlag) not found');
            }
            if (\kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' => \modules\featureFlag\FFlag::FF_KEY_ATTACH_LEAD_TO_HOTEL_ORDER])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . \modules\featureFlag\FFlag::FF_KEY_ATTACH_LEAD_TO_HOTEL_ORDER . ') already exit');
            }

            $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
            $featureFlagService::add(
                \modules\featureFlag\FFlag::FF_KEY_ATTACH_LEAD_TO_HOTEL_ORDER,
                'Attach lead to hotel order',
                \kivork\FeatureFlag\Models\FeatureFlag::TYPE_BOOL,
                false,
                \kivork\FeatureFlag\Models\FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => \modules\featureFlag\FFlag::FF_CATEGORY_SYSTEM,
                    'ff_description' => 'Attach lead to hotel order',
                ]
            );

            \Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                src\helpers\app\AppHelper::throwableLog($throwable),
                'm220927_054714_add_ff_attach_lead_to_hotel_order:safeUp:Throwable'
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
            $featureFlagService::delete(\modules\featureFlag\FFlag::FF_KEY_ATTACH_LEAD_TO_HOTEL_ORDER);

            \Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                src\helpers\app\AppHelper::throwableLog($throwable),
                'm220927_054714_add_ff_attach_lead_to_hotel_order:safeDown:Throwable'
            );
        }
    }
}
