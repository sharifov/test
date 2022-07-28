<?php

use kivork\FeatureFlag\Models\FeatureFlag;
use kivork\FeatureFlag\Services\FeatureFlagService;
use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220728_050949_add_ff_validate_closing_reason_duplicated
 */
class m220728_050949_add_ff_validate_closing_reason_duplicated extends Migration
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

            if (FeatureFlag::find()->where(['ff_key' =>  FFlag::FF_KEY_VALIDATE_CLOSING_REASON_DUPLICATED])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . FFlag::FF_KEY_VALIDATE_CLOSING_REASON_DUPLICATED . ') already exist');
            }

            $featureFlagService = new FeatureFlagService();
            $featureFlagService::add(
                FFlag::FF_KEY_VALIDATE_CLOSING_REASON_DUPLICATED,
                'Validate Closing Reason - Duplicated Enable',
                FeatureFlag::TYPE_BOOL,
                true,
                FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => FFlag::FF_KEY_VALIDATE_CLOSING_REASON_DUPLICATED,
                    'ff_description' => 'Validate Closing Reason - Duplicated Enable',
                ]
            );

            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220728_050949_add_ff_validate_closing_reason_duplicated:safeUp:Throwable');
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
            $featureFlagService::delete(FFlag::FF_KEY_VALIDATE_CLOSING_REASON_DUPLICATED);

            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220728_050949_add_ff_validate_closing_reason_duplicated:safeDown:Throwable');
        }
    }
}
