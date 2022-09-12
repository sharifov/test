<?php

use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220831_061818_add_feature_flag_no_answer_protocol_check_email_in_unsubscribe_list
 */
class m220831_061818_add_feature_flag_no_answer_protocol_check_email_in_unsubscribe_list extends Migration
{
    public function safeUp()
    {
        try {
            if (!class_exists('\kivork\FeatureFlag\Models\FeatureFlag')) {
                throw new \RuntimeException('Class (FeatureFlag) not found');
            }
            if (\kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' => \modules\featureFlag\FFlag::FF_KEY_NO_ANSWER_PROTOCOL_CHECK_EMAIL_IN_UNSUBSCRIBE_LIST])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . \modules\featureFlag\FFlag::FF_KEY_NO_ANSWER_PROTOCOL_CHECK_EMAIL_IN_UNSUBSCRIBE_LIST . ') already exit');
            }

            $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
            $featureFlagService::add(
                \modules\featureFlag\FFlag::FF_KEY_NO_ANSWER_PROTOCOL_CHECK_EMAIL_IN_UNSUBSCRIBE_LIST,
                'No Answer protocol check email in unsubscribe list',
                \kivork\FeatureFlag\Models\FeatureFlag::TYPE_BOOL,
                false,
                \kivork\FeatureFlag\Models\FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => \modules\featureFlag\FFlag::FF_CATEGORY_LEAD,
                    'ff_description' => 'No Answer protocol check email in unsubscribe list'
                ]
            );

            \Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'm220831_061818_add_feature_flag_no_answer_protocol_check_email_in_unsubscribe_list:safeUp:Throwable'
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
            $featureFlagService::delete(\modules\featureFlag\FFlag::FF_KEY_NO_ANSWER_PROTOCOL_CHECK_EMAIL_IN_UNSUBSCRIBE_LIST);

            \Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'm220831_061818_add_feature_flag_no_answer_protocol_check_email_in_unsubscribe_list:safeDown:Throwable'
            );
        }
    }
}
