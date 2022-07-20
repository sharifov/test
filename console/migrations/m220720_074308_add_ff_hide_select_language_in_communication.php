<?php

use kivork\FeatureFlag\Models\FeatureFlag;
use kivork\FeatureFlag\Services\FeatureFlagService;
use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220720_074308_add_ff_hide_select_language_in_communication
 */
class m220720_074308_add_ff_hide_select_language_in_communication extends Migration
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

            if (FeatureFlag::find()->where(['ff_key' =>  FFlag::FF_KEY_HIDE_LANGUAGE_FIELD_COMMUNICATION_BLOCK])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . FFlag::FF_KEY_HIDE_LANGUAGE_FIELD_COMMUNICATION_BLOCK . ') already exist');
            }

            $featureFlagService = new FeatureFlagService();
            $featureFlagService::add(
                FFlag::FF_KEY_HIDE_LANGUAGE_FIELD_COMMUNICATION_BLOCK,
                'Hide Language Field In CommunicationBlock Enable',
                FeatureFlag::TYPE_BOOL,
                true,
                FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => FFlag::FF_KEY_HIDE_LANGUAGE_FIELD_COMMUNICATION_BLOCK,
                    'ff_description' => 'Hide Language Field In CommunicationBlock Enable',
                ]
            );

            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220720_074308_add_ff_hide_select_language_in_communication:safeUp:Throwable');
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
            $featureFlagService::delete(FFlag::FF_KEY_HIDE_LANGUAGE_FIELD_COMMUNICATION_BLOCK);

            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220720_074308_add_ff_hide_select_language_in_communication:safeDown:Throwable');
        }
    }
}
