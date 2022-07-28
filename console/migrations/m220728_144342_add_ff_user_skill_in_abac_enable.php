<?php

use kivork\FeatureFlag\Models\FeatureFlag;
use kivork\FeatureFlag\Services\FeatureFlagService;
use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220728_144342_add_ff_user_skill_in_abac_enable
 */
class m220728_144342_add_ff_user_skill_in_abac_enable extends Migration
{
    public function safeUp()
    {
        try {
            if (!class_exists('\kivork\FeatureFlag\Models\FeatureFlag')) {
                throw new \RuntimeException('Class (FeatureFlag) not found');
            }

            if (FeatureFlag::find()->where(['ff_key' =>  FFlag::FF_KEY_USER_SKILL_IN_ABAC_ENABLE])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . FFlag::FF_KEY_USER_SKILL_IN_ABAC_ENABLE . ') already exist');
            }

            $featureFlagService = new FeatureFlagService();
            $featureFlagService::add(
                FFlag::FF_KEY_USER_SKILL_IN_ABAC_ENABLE,
                'User Skill in abac parameters enable',
                FeatureFlag::TYPE_BOOL,
                true,
                FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => FFlag::FF_CATEGORY_SYSTEM,
                    'ff_description' => 'User Skill in abac parameters enable',
                ]
            );

            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220728_075031_add_ff_smart_lead_distribution_enable:safeUp:Throwable');
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
            $featureFlagService::delete(FFlag::FF_KEY_USER_SKILL_IN_ABAC_ENABLE);

            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220728_075031_add_ff_smart_lead_distribution_enable:safeDown:Throwable');
        }
    }
}
