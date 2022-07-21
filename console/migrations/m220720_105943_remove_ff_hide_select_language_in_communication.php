<?php

use kivork\FeatureFlag\Models\FeatureFlag;
use kivork\FeatureFlag\Services\FeatureFlagService;
use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220720_105943_remove_ff_hide_select_language_in_communication
 */
class m220720_105943_remove_ff_hide_select_language_in_communication extends Migration
{
    private const FF_KEY_HIDE_LANGUAGE_FIELD_COMMUNICATION_BLOCK = 'hideLanguageFieldInCommunicationBlock';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            if (!class_exists('\kivork\FeatureFlag\Models\FeatureFlag')) {
                throw new \RuntimeException('Class (FeatureFlag) not found');
            }

            $featureFlagService = new FeatureFlagService();
            $featureFlagService::delete(self::FF_KEY_HIDE_LANGUAGE_FIELD_COMMUNICATION_BLOCK);

            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220720_105943_remove_ff_hide_select_language_in_communication:safeDown:Throwable');
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

            if (FeatureFlag::find()->where(['ff_key' =>  self::FF_KEY_HIDE_LANGUAGE_FIELD_COMMUNICATION_BLOCK])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . self::FF_KEY_HIDE_LANGUAGE_FIELD_COMMUNICATION_BLOCK . ') already exist');
            }

            $featureFlagService = new FeatureFlagService();
            $featureFlagService::add(
                self::FF_KEY_HIDE_LANGUAGE_FIELD_COMMUNICATION_BLOCK,
                'Hide Language Field In CommunicationBlock Enable',
                FeatureFlag::TYPE_BOOL,
                true,
                FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => self::FF_KEY_HIDE_LANGUAGE_FIELD_COMMUNICATION_BLOCK,
                    'ff_description' => 'Hide Language Field In CommunicationBlock Enable',
                ]
            );
            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220720_105943_remove_ff_hide_select_language_in_communication:safeUp:Throwable');
        }
    }
}
