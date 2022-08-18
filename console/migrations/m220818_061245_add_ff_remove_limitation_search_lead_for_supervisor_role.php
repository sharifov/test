<?php

use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220818_061245_add_ff_remove_limitation_search_lead_for_supervisor_role
 */
class m220818_061245_add_ff_remove_limitation_search_lead_for_supervisor_role extends Migration
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
            if (\kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' => \modules\featureFlag\FFlag::FF_KEY_REMOVE_LIMITATION_SUPERVISION_IN_LEAD_SEARCH])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . \modules\featureFlag\FFlag::FF_KEY_REMOVE_LIMITATION_SUPERVISION_IN_LEAD_SEARCH . ') already exit');
            }

            $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
            $featureFlagService::add(
                \modules\featureFlag\FFlag::FF_KEY_REMOVE_LIMITATION_SUPERVISION_IN_LEAD_SEARCH,
                'Remove User Group limitation for Search Leads for Sale Supervisor role',
                \kivork\FeatureFlag\Models\FeatureFlag::TYPE_BOOL,
                false,
                \kivork\FeatureFlag\Models\FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => \modules\featureFlag\FFlag::FF_CATEGORY_LEAD,
                    'ff_description' => 'Remove User Group limitation for Search Leads for Sale Supervisor role'
                ]
            );

            \Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'm220818_061245_add_ff_remove_limitation_search_lead_for_supervisor_role:safeUp:Throwable'
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
            $featureFlagService::delete(\modules\featureFlag\FFlag::FF_KEY_REMOVE_LIMITATION_SUPERVISION_IN_LEAD_SEARCH);

            \Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'm220818_061245_add_ff_remove_limitation_search_lead_for_supervisor_role:safeDown:Throwable'
            );
        }
    }
}
