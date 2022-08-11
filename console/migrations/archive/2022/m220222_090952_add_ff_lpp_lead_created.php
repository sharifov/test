<?php

use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220222_090952_add_ff_lpp_lead_created
 */
class m220222_090952_add_ff_lpp_lead_created extends Migration
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
            if (\kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' => FFlag::FF_KEY_LPP_LEAD_CREATED])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . FFlag::FF_KEY_LPP_LEAD_CREATED . ') already exit');
            }
            $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
            $featureFlagService::add(
                FFlag::FF_KEY_LPP_LEAD_CREATED,
                'Lead Poor Processing Lead created',
                \kivork\FeatureFlag\Models\FeatureFlag::TYPE_STRING,
                '2022-02-22 08:00:00',
                \kivork\FeatureFlag\Models\FeatureFlag::ET_ENABLED,
                [
                    'ff_category' => FFlag::FF_CATEGORY_LEAD,
                    'ff_description' => 'Lead Poor Processing DT lead created restriction for rule Scheduled communication'
                ]
            );
            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'm220222_090952_add_ff_lpp_lead_created:safeUp:Throwable');
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
            $featureFlagService::delete(FFlag::FF_KEY_LPP_LEAD_CREATED);
            Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'm220222_090952_add_ff_lpp_lead_created:safeDown:Throwable');
        }
    }
}
