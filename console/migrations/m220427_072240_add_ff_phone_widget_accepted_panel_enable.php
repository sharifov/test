<?php

use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220427_072240_add_ff_phone_widget_accepted_panel_enable
 */
class m220427_072240_add_ff_phone_widget_accepted_panel_enable extends Migration
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
            if (\kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' => FFlag::FF_KEY_PHONE_WIDGET_ACCEPTED_PANEL_ENABLED])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . FFlag::FF_KEY_PHONE_WIDGET_ACCEPTED_PANEL_ENABLED . ') already exit');
            }
            $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
            $featureFlagService::add(
                FFlag::FF_KEY_PHONE_WIDGET_ACCEPTED_PANEL_ENABLED,
                'Phone widget accepted panel enabled',
                \kivork\FeatureFlag\Models\FeatureFlag::TYPE_BOOL,
                'true',
                \kivork\FeatureFlag\Models\FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => FFlag::FF_CATEGORY_VOIP,
                    'ff_description' => 'Voip'
                ]
            );
            Yii::$app->ff->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'm220427_072240_add_ff_phone_widget_accepted_panel_enable:safeUp:Throwable');
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
            $featureFlagService::delete(FFlag::FF_KEY_PHONE_WIDGET_ACCEPTED_PANEL_ENABLED);
            Yii::$app->ff->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'm220427_072240_add_ff_phone_widget_accepted_panel_enable:safeDown:Throwable');
        }
    }
}
