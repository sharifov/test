<?php

use yii\db\Migration;
use modules\featureFlag\FFlag;

/**
 * Class m220623_151120_add_ff_email_normalized
 */
class m220623_151120_add_ff_email_normalized extends Migration
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
            if (\kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' => FFlag::FF_KEY_EMAIL_NORMALIZED_FORM_ENABLE])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . FFlag::FF_KEY_EMAIL_NORMALIZED_FORM_ENABLE . ') already exit');
            }
            $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
            $featureFlagService::add(
                FFlag::FF_KEY_EMAIL_NORMALIZED_FORM_ENABLE,
                'Using Email normalized form enable\disable',
                \kivork\FeatureFlag\Models\FeatureFlag::TYPE_BOOL,
                'true',
                \kivork\FeatureFlag\Models\FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => FFlag::FF_CATEGORY_SYSTEM,
                    'ff_description' => 'Using Email normalized form enable\disable'
                ]
            );
            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'm220623_151120_add_ff_email_normalized:safeUp:Throwable');
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
            $featureFlagService::delete(\modules\featureFlag\FFlag::FF_KEY_EMAIL_NORMALIZED_FORM_ENABLE);
            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(\src\helpers\app\AppHelper::throwableLog($throwable), 'm220623_151120_add_ff_email_normalized:safeDown:Throwable');
        }
    }
}
