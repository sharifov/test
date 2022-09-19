<?php

use yii\db\Migration;
use src\helpers\app\AppHelper;

/**
 * Class m220919_080246_add_ff_hide_task_info_column_from_lead_section_ui
 */
class m220919_080246_add_ff_hide_task_info_column_from_lead_section_ui extends Migration
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
            if (\kivork\FeatureFlag\Models\FeatureFlag::find()->where(['ff_key' => \modules\featureFlag\FFlag::FF_KEY_HIDE_TASK_INFO_COLUMN_FROM_LEAD_SECTION_UI])->exists()) {
                throw new \RuntimeException('FeatureFlag (' . \modules\featureFlag\FFlag::FF_KEY_HIDE_TASK_INFO_COLUMN_FROM_LEAD_SECTION_UI . ') already exit');
            }

            $featureFlagService = new \kivork\FeatureFlag\Services\FeatureFlagService();
            $featureFlagService::add(
                \modules\featureFlag\FFlag::FF_KEY_HIDE_TASK_INFO_COLUMN_FROM_LEAD_SECTION_UI,
                'Hide "Task info" column from Lead section on UI',
                \kivork\FeatureFlag\Models\FeatureFlag::TYPE_BOOL,
                false,
                \kivork\FeatureFlag\Models\FeatureFlag::ET_DISABLED,
                [
                    'ff_category' => \modules\featureFlag\FFlag::FF_CATEGORY_LEAD,
                    'ff_description' => 'Hide "Task info" column from Lead section on UI',
                ]
            );

            \Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'm220919_080246_add_ff_hide_task_info_column_from_lead_section_ui:safeUp:Throwable'
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
            $featureFlagService::delete(\modules\featureFlag\FFlag::FF_KEY_HIDE_TASK_INFO_COLUMN_FROM_LEAD_SECTION_UI);

            \Yii::$app->featureFlag->invalidateCache();
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'm220919_080246_add_ff_hide_task_info_column_from_lead_section_ui:safeDown:Throwable'
            );
        }
    }
}
