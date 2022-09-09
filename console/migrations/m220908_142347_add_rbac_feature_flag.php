<?php

use kivork\FeatureFlag\Services\FeatureFlagService;
use kivork\FeatureFlag\Models\FeatureFlag;
use modules\featureFlag\FFlag;
use yii\db\Migration;

/**
 * Class m220908_142347_add_rbac_feature_flag
 */
class m220908_142347_add_rbac_feature_flag extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            if (!class_exists(FeatureFlag::class)) {
                throw new \RuntimeException('Class (FeatureFlag) not found');
            }
            if (FeatureFlag::find()->where(['ff_key' => FFlag::FF_KEY_BO_API_RBAC_AUTH])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . FFlag::FF_KEY_BO_API_RBAC_AUTH . ') already exit');
            }

            $ff = new FeatureFlagService();
            $ff->add(
                FFlag::FF_KEY_BO_API_RBAC_AUTH,
                'Backoffice API RBAC authorization',
                FeatureFlag::TYPE_BOOL,
                false,
                FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => FFlag::FF_CATEGORY_SYSTEM,
                    'ff_description' => 'Require API User authorization'
                ]
            );
            \Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'm220908_142347_add_rbac_feature_flag:safeUp:Throwable'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            if (!class_exists(FeatureFlag::class)) {
                throw new \RuntimeException('Class (FeatureFlag) not found');
            }

            $featureFlagService = new FeatureFlagService();
            $featureFlagService::delete(FFlag::FF_KEY_BO_API_RBAC_AUTH);

            \Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'm220908_142347_add_rbac_feature_flag:safeDown:Throwable'
            );
        }
    }
}
