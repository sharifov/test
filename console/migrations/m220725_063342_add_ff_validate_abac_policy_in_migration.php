<?php

use kivork\FeatureFlag\Models\FeatureFlag;
use kivork\FeatureFlag\Services\FeatureFlagService;
use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220725_063342_add_ff_validate_abac_policy_in_migration
 */
class m220725_063342_add_ff_validate_abac_policy_in_migration extends Migration
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

            if (FeatureFlag::find()->where(['ff_key' =>  FFlag::FF_KEY_VALIDATE_ABAC_POLICY_IN_MIGRATION])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . FFlag::FF_KEY_VALIDATE_ABAC_POLICY_IN_MIGRATION . ') already exist');
            }

            $featureFlagService = new FeatureFlagService();
            $featureFlagService::add(
                FFlag::FF_KEY_VALIDATE_ABAC_POLICY_IN_MIGRATION,
                'Validate Abac policy in migration Enable',
                FeatureFlag::TYPE_BOOL,
                true,
                FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => FFlag::FF_KEY_VALIDATE_ABAC_POLICY_IN_MIGRATION,
                    'ff_description' => 'Validate Abac policy in migration Enable',
                ]
            );

            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220725_063342_add_ff_validate_abac_policy_in_migration:safeUp:Throwable');
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
            $featureFlagService::delete(FFlag::FF_KEY_VALIDATE_ABAC_POLICY_IN_MIGRATION);

            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220725_063342_add_ff_validate_abac_policy_in_migration:safeDown:Throwable');
        }
    }
}
